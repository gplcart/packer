<?php

/**
 * @package Packer
 * @author Iurii Makukh <gplcart.software@gmail.com>
 * @copyright Copyright (c) 2018, Iurii Makukh
 * @license https://www.gnu.org/licenses/gpl.html GNU/GPLv3
 */

namespace gplcart\modules\packer\controllers;

use Exception;
use gplcart\core\controllers\backend\Controller;
use gplcart\core\models\Convertor as ConvertorModel;
use gplcart\core\models\Product as ProductModel;
use gplcart\modules\packer\models\Box as BoxModel;
use gplcart\modules\packer\models\Packer as PackerModel;

/**
 * Handles incoming requests and outputs data related to boxes
 */
class Box extends Controller
{

    /**
     * Box model class instance
     * @var \gplcart\modules\packer\models\Box $box
     */
    protected $box;

    /**
     * Packer model class instance
     * @var \gplcart\modules\packer\models\Packer $packer
     */
    protected $packer;

    /**
     * Product model class instance
     * @var \gplcart\core\models\Product $product
     */
    protected $product;

    /**
     * Convertor model class instance
     * @var \gplcart\core\models\Convertor $convertor
     */
    protected $convertor;

    /**
     * Pager limit
     * @var array
     */
    protected $data_limit;

    /**
     * The current updating box
     * @var array
     */
    protected $data_box = array();

    /**
     * Box constructor.
     * @param BoxModel $box
     * @param PackerModel $packer
     * @param ProductModel $product
     * @param ConvertorModel $convertor
     */
    public function __construct(BoxModel $box, PackerModel $packer, ProductModel $product, ConvertorModel $convertor)
    {
        parent::__construct();

        $this->box = $box;
        $this->packer = $packer;
        $this->product = $product;
        $this->convertor = $convertor;
    }

    /**
     * Route callback
     * Displays the box overview page
     */
    public function listBox()
    {
        $this->actionListBox();
        $this->setTitleListBox();
        $this->setBreadcrumbListBox();
        $this->setFilterListBox();
        $this->setPagerListBox();

        $this->setData('boxes', $this->getListBox());
        $this->outputListBox();
    }

    /**
     * Applies an action to the selected boxes
     */
    protected function actionListBox()
    {
        list($selected, $action) = $this->getPostedAction();

        $deleted = 0;

        foreach ($selected as $id) {
            if ($action === 'delete' && $this->access('module_packer_box_delete')) {
                $deleted += (int) $this->box->delete($id);
            }
        }

        if ($deleted > 0) {
            $message = $this->text('Deleted %num item(s)', array('%num' => $deleted));
            $this->setMessage($message, 'success');
        }
    }

    /**
     * Sets filter parameters on the box overview page
     */
    protected function setFilterListBox()
    {
        $filter = array(
            'created', 'name', 'box_id',
            'status', 'inner_width', 'inner_length',
            'inner_depth', 'max_weight', 'weight_unit',
            'size_unit', 'shipping_method'
        );

        $this->setFilter($filter);
    }

    /**
     * Sets pager on the box overview page
     * @return array
     */
    protected function setPagerListBox()
    {
        $options = $this->query_filter;
        $options['count'] = true;

        $pager = array(
            'query' => $this->query_filter,
            'total' => (int) $this->box->getList($options)
        );

        return $this->data_limit = $this->setPager($pager);
    }

    /**
     * Returns an array of boxes
     * @return array
     */
    protected function getListBox()
    {
        $options = $this->query_filter;
        $options['limit'] = $this->data_limit;

        return $this->box->getList($options);
    }

    /**
     * Sets title on the box overview page
     */
    protected function setTitleListBox()
    {
        $this->setTitle($this->text('Boxes'));
    }

    /**
     * Sets breadcrumbs on the box overview page
     */
    protected function setBreadcrumbListBox()
    {
        $breadcrumb = array(
            'url' => $this->url('admin'),
            'text' => $this->text('Dashboard')
        );

        $this->setBreadcrumb($breadcrumb);
    }

    /**
     * Render and output the box overview page
     */
    protected function outputListBox()
    {
        $this->output('packer|box/list');
    }

    /**
     * Page callback
     * Displays the fit items page
     * @param $box_id
     */
    public function fitBox($box_id)
    {
        $this->setBox((int) $box_id);
        $this->setTitleFitBox();
        $this->setBreadcrumbEditBox();

        $this->setData('box', $this->data_box);

        $this->submitFitBox();
        $this->setDataFitBox();
        $this->outputFitBox();
    }

    /**
     * Prepare and set variables for template
     */
    protected function setDataFitBox()
    {
        $product_id = $this->getData('box.product_id');

        if (is_array($product_id)) {
            $this->setData('box.product_id', implode(PHP_EOL, $product_id));
        }
    }


    /**
     * Sets titles on the fit box page
     */
    protected function setTitleFitBox()
    {
        $text = $this->text('Fit products into box %name', array('%name' => $this->data_box['name']));
        $this->setTitle($text);
    }

    /**
     * Handles submitted data while trying to fit products into a box
     */
    protected function submitFitBox()
    {
        if ($this->isPosted('fit') && $this->validateFitBox()) {
            $this->doFitBox();
        }
    }

    /**
     * Validates submitted data while trying to fit products into a box
     * @return bool
     */
    protected function validateFitBox()
    {
        $this->setSubmitted('box');
        $this->setSubmittedArray('product_id');
        $this->validateElement('product_id', 'required');
        $this->validateProductsFitBox();

        return !$this->hasErrors();
    }

    /**
     * Validates an array of submitted product IDs
     */
    protected function validateProductsFitBox()
    {
        $errors = $products = array();

        foreach ((array) $this->getSubmitted('product_id', array()) as $line => $product_id) {

            $line++;

            if (!is_numeric($product_id)) {
                $errors[] = $this->text('Error on line @num: @error', array(
                    '@num' => $line,
                    '@error' => $this->text('Product ID must be numeric')));
                continue;
            }

            $product = $this->product->get($product_id);

            if (empty($product)) {
                $errors[] = $this->text('Error on line @num: @error', array(
                    '@num' => $line,
                    '@error' => $this->text('Product does not exist')));
                continue;
            }

            if (empty($product['length']) || empty($product['width']) || empty($product['height'])) {
                $errors[] = $this->text('Error on line @num: @error', array(
                    '@num' => $line,
                    '@error' => $this->text('At least one dimension parameter is undefined')));
                continue;
            }

            if (empty($product['weight'])) {
                $errors[] = $this->text('Error on line @num: @error', array(
                    '@num' => $line,
                    '@error' => $this->text('Unknown weight')));
                continue;
            }

            $products[] = $product;
        }

        if (!empty($errors)) {
            $this->setError('product_id', $errors);
        }

        $this->setSubmitted('products', $products);
    }

    /**
     * Tries to fit the submitted products into a box
     */
    protected function doFitBox()
    {
        try {
            $products = $this->getSubmitted('products');
            $items = $this->packer->getFitItems($products, $this->data_box);
        } catch (Exception $ex) {
            $items = array();
            $this->setMessage($ex->getMessage(), 'warning');
        }

        if (empty($items)) {
            $this->setMessage($this->text('Nothing fits into the box'));
        } else {
            $this->setData('items', $items);
        }

        $this->setData('box.product_id', $this->getSubmitted('product_id'));
    }

    /**
     * Render and output the box fit items page
     */
    protected function outputFitBox()
    {
        $this->output('packer|box/fit');
    }


    /**
     * Page callback
     * Displays the edit box page
     * @param null|int $box_id
     */
    public function editBox($box_id = null)
    {
        $this->setBox($box_id);
        $this->setTitleEditBox();
        $this->setBreadcrumbEditBox();

        $this->setData('box', $this->data_box);
        $this->setData('fields', $this->getDimensionFieldsBox());
        $this->setData('size_units', $this->convertor->getUnitNames('size'));
        $this->setData('weight_units', $this->convertor->getUnitNames('weight'));

        $this->submitEditBox();
        $this->outputEditBox();
    }

    /**
     * Sets the box data
     * @param $box_id
     */
    protected function setBox($box_id)
    {
        if (is_numeric($box_id)) {
            $this->data_box = $this->box->get($box_id);
            if (empty($this->data_box)) {
                $this->outputHttpStatus(403);
            }
        }
    }

    /**
     * Sets titles on the edit box page
     */
    protected function setTitleEditBox()
    {
        if (isset($this->data_box['box_id'])) {
            $text = $this->text('Edit %name', array('%name' => $this->data_box['box_id']));
        } else {
            $text = $this->text('Add box');
        }

        $this->setTitle($text);
    }

    /**
     * Sets breadcrumbs on the box edit page
     */
    protected function setBreadcrumbEditBox()
    {
        $breadcrumbs = array();

        $breadcrumbs[] = array(
            'url' => $this->url('admin'),
            'text' => $this->text('Dashboard')
        );

        $breadcrumbs[] = array(
            'url' => $this->url('admin/settings/box'),
            'text' => $this->text('Boxes')
        );

        $this->setBreadcrumbs($breadcrumbs);
    }

    /**
     * Handles a submitted box
     */
    protected function submitEditBox()
    {
        if ($this->isPosted('delete') && isset($this->data_box['box_id'])) {
            $this->deleteBox();
        } else if ($this->isPosted('save') && $this->validateEditBox()) {
            if (isset($this->data_box['box_id'])) {
                $this->updateBox();
            } else {
                $this->addBox();
            }
        }
    }

    /**
     * Validates a submitted box data
     */
    protected function validateEditBox()
    {
        $this->setSubmitted('box');
        $this->setSubmittedBool('status');

        $this->validateElement('name', 'length', array(1, 255));
        $this->validateElement('shipping_method', 'length', array(0, 255));

        foreach (array_keys($this->getDimensionFieldsBox()) as $field) {
            $this->validateElement($field, 'numeric');
            $this->validateElement($field, 'length', array(1, 10));
        }

        $unit = $this->getSubmitted('size_unit');
        $units = $this->convertor->getUnitNames('size');

        if (empty($unit) || empty($units[$unit])) {
            $this->setError('size_unit', $this->text('Invalid size measurement unit'));
        }

        $unit = $this->getSubmitted('weight_unit');
        $units = $this->convertor->getUnitNames('weight');

        if (empty($unit) || empty($units[$unit])) {
            $this->setError('weight_unit', $this->text('Invalid weight measurement unit'));
        }

        return !$this->hasErrors();
    }

    /**
     * Returns an array of box dimension field names keyed by DB fields
     * @return array
     */
    protected function getDimensionFieldsBox()
    {
        $fields = array('outer_width', 'outer_length',
            'outer_depth', 'empty_weight', 'inner_width',
            'inner_width', 'inner_length', 'inner_depth', 'max_weight');

        $names = array();

        foreach ($fields as $field) {
            $names[$field] = ucfirst(str_replace('_', ' ', $field));
        }

        return $names;
    }

    /**
     * Updates a submitted box
     */
    protected function updateBox()
    {
        $this->controlAccess('module_packer_box_edit');

        if ($this->box->update($this->data_box['box_id'], $this->getSubmitted())) {
            $this->redirect('admin/settings/box', $this->text('Box has been updated'), 'success');
        }

        $this->redirect('', $this->text('Box has not been updated'), 'warning');
    }

    /**
     * Adds a new box
     */
    protected function addBox()
    {
        $this->controlAccess('module_packer_box_add');

        if ($this->box->add($this->getSubmitted())) {
            $this->redirect('admin/settings/box', $this->text('Box has been added'), 'success');
        }

        $this->redirect('', $this->text('Box has not been added'), 'warning');
    }

    /**
     * Delete a submitted box
     */
    protected function deleteBox()
    {
        $this->controlAccess('module_packer_box_delete');

        if ($this->box->delete($this->data_box['box_id'])) {
            $this->redirect('admin/settings/box', $this->text('Box has been deleted'), 'success');
        }

        $this->redirect('', $this->text('Box has not been deleted'), 'warning');
    }

    /**
     * Render and output the box edit page
     */
    protected function outputEditBox()
    {
        $this->output('packer|box/edit');
    }

}
