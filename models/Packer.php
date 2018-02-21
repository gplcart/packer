<?php

/**
 * @package Packer
 * @author Iurii Makukh <gplcart.software@gmail.com>
 * @copyright Copyright (c) 2018, Iurii Makukh <gplcart.software@gmail.com>
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPL-3.0-or-later
 */

namespace gplcart\modules\packer\models;

use DVDoug\BoxPacker\ItemList;
use DVDoug\BoxPacker\VolumePacker;
use gplcart\core\Library;
use gplcart\core\models\Convertor;
use gplcart\modules\packer\helpers\Box as BoxHelper;
use gplcart\modules\packer\helpers\Item as ItemHelper;
use LogicException;
use OutOfRangeException;
use UnexpectedValueException;

/**
 * Manages basic behaviors and data related to Packer module
 */
class Packer
{

    /**
     * Library class instance
     * @var \gplcart\core\Library $library
     */
    protected $library;

    /**
     * Convertor model class instance
     * @var \gplcart\core\models\Convertor $convertor
     */
    protected $convertor;

    /**
     * Box model class instanceS
     * @var \gplcart\modules\packer\models\Box $box
     */
    protected $box;

    /**
     * Packer constructor.
     * @param Library $library
     * @param Convertor $convertor
     * @param Box $box
     */
    public function __construct(Library $library, Convertor $convertor, Box $box)
    {
        $this->library = $library;
        $this->convertor = $convertor;
        $this->box = $box;
    }

    /**
     * Returns library class instance
     * @return \DVDoug\BoxPacker\Packer
     * @throws LogicException
     */
    public function getPacker()
    {
        $this->library->load('packer');

        if (!class_exists('DVDoug\BoxPacker\Packer')) {
            throw new \LogicException('Class \DVDoug\BoxPacker\Packer not found');
        }

        return new \DVDoug\BoxPacker\Packer;
    }

    /**
     * Pack a set of items into a given set of box types
     * @param array $items
     * @param array $boxes
     * @return \DVDoug\BoxPacker\PackedBoxList
     */
    public function pack(array $items, array $boxes)
    {
        $packer = $this->getPacker();

        $this->prepareBoxes($boxes, $items);

        foreach ($boxes as $box) {
            $packer->addBox(new BoxHelper($box));
        }

        foreach ($items as $item) {
            $packer->addItem(new ItemHelper($item));
        }

        return $packer->pack();
    }

    /**
     * Returns an array of boxes needed to fit all the order items
     * @param array $order
     * @param array $cart
     * @return array
     * @throws OutOfRangeException
     * @throws UnexpectedValueException
     */
    public function packOrder(array $order, array $cart)
    {
        if (!isset($order['shipping'])) {
            throw new OutOfRangeException('"shipping" key is not set in the order data');
        }

        if (!isset($order['volume'])) {
            throw new OutOfRangeException('"volume" key is not set in the order data');
        }

        if (!isset($cart['items'])) {
            throw new OutOfRangeException('"items" key is not set in the cart data');
        }

        $boxes = $this->box->getListByShippingMethod($order['shipping']);

        if (empty($boxes)) {
            throw new UnexpectedValueException('No enabled boxes found in the database for the shipping method');
        }

        $items = array();

        foreach ($cart['items'] as $item) {
            $product = $item['product'];
            $product['volume'] = $order['volume'];
            $items[] = $product;
        }

        return $this->getPackedBoxes($items, $boxes);
    }

    /**
     * Returns an array of packed boxes
     * @param array $items
     * @param array $boxes
     * @return array
     */
    public function getPackedBoxes(array $items, array $boxes)
    {
        $packed = array();

        foreach ($this->pack($items, $boxes) as $pack) {
            /** @var \gplcart\modules\packer\helpers\Box $box */
            $box = $pack->getBox();
            $data = $box->getBoxData();
            $packed[$data['box_id']] = $data;
        }

        return $packed;
    }

    /**
     * Does an array of items fit into the box
     * @param array $items
     * @param array $box
     * @return \DVDoug\BoxPacker\PackedBox
     */
    public function fit(array $items, array $box)
    {
        $this->getPacker();
        $this->prepareItems($items, $box);

        $box_object = new BoxHelper($box);
        $items_object = new ItemList();

        foreach ($items as $item) {
            $items_object->insert(new ItemHelper($item));
        }

        $packer = new VolumePacker($box_object, $items_object);
        return $packer->pack();
    }

    /**
     * Returns an array of items that fit into the box
     * @param array $items
     * @param array $box
     * @return array
     */
    public function getFitItems(array $items, array $box)
    {
        $fit = array();

        /** @var \gplcart\modules\packer\helpers\Item $item */
        foreach ($this->fit($items, $box)->getItems() as $item) {
            $fit[] = $item->getItemData();
        }

        return $fit;
    }

    /**
     * Prepare an array of boxes
     * @param array $boxes
     * @param array $items
     */
    protected function prepareBoxes(array &$boxes, array $items)
    {
        if (!empty($boxes) && !empty($items)) {
            $item = reset($items);
            foreach ($boxes as &$box) {
                $this->convertBox($box, $item);
            }
        }
    }

    /**
     * Prepare an array of items
     * @param array $items
     * @param array $box
     */
    protected function prepareItems(array &$items, array $box)
    {
        foreach ($items as &$item) {
            $this->convertItem($item, $box);
        }
    }

    /**
     * Converts unints in the box data
     * @param array $box
     * @param array $item
     */
    protected function convertBox(array &$box, array $item)
    {
        if ($box['size_unit'] !== $item['size_unit']) {

            $fields = array(
                'outer_width', 'outer_length',
                'outer_depth', 'inner_width',
                'inner_length', 'inner_depth'
            );

            foreach ($fields as $field) {
                $box[$field] = $this->convertor->convert($box[$field], $box['size_unit'], $item['size_unit']);
            }

            $box['size_unit'] = $item['size_unit'];
        }

        if ($box['weight_unit'] !== $item['weight_unit']) {

            foreach (array('max_weight', 'empty_weight') as $field) {
                $box[$field] = $this->convertor->convert($box[$field], $box['weight_unit'], $item['weight_unit']);
            }

            $box['weight_unit'] = $item['weight_unit'];
        }
    }

    /**
     * Convert item units
     * @param array $item
     * @param array $box
     */
    protected function convertItem(array &$item, array $box)
    {
        if ($item['size_unit'] !== $box['size_unit']) {

            foreach (array('length', 'width', 'height') as $field) {
                $item[$field] = $this->convertor->convert($item[$field], $item['size_unit'], $box['size_unit']);
            }

            $item['size_unit'] = $box['size_unit'];
        }

        if ($item['weight_unit'] !== $box['weight_unit']) {
            $item['weight'] = $this->convertor->convert($item['weight'], $item['weight_unit'], $box['weight_unit']);
            $item['weight_unit'] = $box['weight_unit'];
        }

        $item['volume'] = $item['length'] * $item['width'] * $item['height'];
    }
}
