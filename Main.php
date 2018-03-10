<?php

/**
 * @package Packer
 * @author Iurii Makukh <gplcart.software@gmail.com>
 * @copyright Copyright (c) 2018, Iurii Makukh <gplcart.software@gmail.com>
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPL-3.0-or-later
 */

namespace gplcart\modules\packer;

use Exception;
use gplcart\core\Config;
use gplcart\core\Container;

/**
 * Main class for Packer module
 */
class Main
{

    /**
     * Database class instance
     * @var \gplcart\core\Database $db
     */
    protected $db;

    /**
     * Main constructor.
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->db = $config->getDb();
        $this->db->addScheme($this->getDbScheme());
    }

    /**
     * Implements hook "library.list"
     * @param array $libraries
     */
    public function hookLibraryList(array &$libraries)
    {
        $libraries['packer'] = array(
            'name' => 'Box packer', // @text
            'description' => '4D bin packing / knapsack problem solver', // @text
            'url' => 'https://github.com/dvdoug/BoxPacker',
            'download' => 'https://github.com/dvdoug/BoxPacker/archive/2.4.3.zip',
            'type' => 'php',
            'version' => '2.4.3',
            'module' => 'packer',
            'vendor' => 'dvdoug/boxpacker'
        );
    }

    /**
     * Implements hook "module.uninstall.after"
     */
    public function hookModuleUninstallAfter()
    {
        $this->db->deleteTable('module_packer_boxes');
    }

    /**
     * Implements hook "module.install.before"
     * @param null|string
     */
    public function hookModuleInstallBefore(&$result)
    {
        try {
            $this->db->importScheme('module_packer_boxes', $this->getDbScheme());
        } catch (Exception $ex) {
            $result = $ex->getMessage();
        }
    }

    /**
     * Implements hook "route.list"
     * @param array $routes
     */
    public function hookRouteList(array &$routes)
    {
        $routes['admin/settings/box'] = array(
            'menu' => array(
                'admin' => 'Boxes' // @text
            ),
            'access' => 'module_packer_box',
            'handlers' => array(
                'controller' => array('gplcart\\modules\\packer\\controllers\\Box', 'listBox')
            )
        );

        $routes['admin/settings/box/add'] = array(
            'access' => 'module_packer_box_add',
            'handlers' => array(
                'controller' => array('gplcart\\modules\\packer\\controllers\\Box', 'editBox')
            )
        );

        $routes['admin/settings/box/fit/(\d+)'] = array(
            'access' => 'module_packer_box',
            'handlers' => array(
                'controller' => array('gplcart\\modules\\packer\\controllers\\Box', 'fitBox')
            )
        );

        $routes['admin/settings/box/edit/(\d+)'] = array(
            'access' => 'module_packer_box_edit',
            'handlers' => array(
                'controller' => array('gplcart\\modules\\packer\\controllers\\Box', 'editBox')
            )
        );
    }

    /**
     * Implements hook "order.add.before"
     * @param array $data
     */
    public function hookOrderAddBefore(array &$data)
    {
        if (!empty($data['cart']['items'])) {

            try {
                $data['data']['packages'] = $this->packOrder($data, $data['cart']);
            } catch (Exception $ex) {
                trigger_error($ex->getMessage());
            }
        }
    }

    /**
     * Implements hook "user.role.permissions"
     * @param array $permissions
     */
    public function hookUserRolePermissions(array &$permissions)
    {
        $permissions['module_packer_box'] = 'Packer: access boxes'; // @text
        $permissions['module_packer_box_add'] = 'Packer: add box'; // @text
        $permissions['module_packer_box_edit'] = 'Packer: edit box'; // @text
        $permissions['module_packer_box_delete'] = 'Packer: delete box'; // @text
    }


    /**
     * Returns an array of boxes needed to fit all the order items
     * @param array $order
     * @param array $cart
     * @return array
     */
    public function packOrder(array $order, array $cart)
    {
        return $this->getPackerModel()->packOrder($order, $cart);
    }

    /**
     * Pack an array of items into a given array of boxes
     * @param array $items
     * @param array $boxes
     * @return array
     */
    public function getPackedBoxes(array $items, array $boxes)
    {
        return $this->getPackerModel()->getPackedBoxes($items, $boxes);
    }

    /**
     * Returns an array of items that fit into the box
     * @param array $items
     * @param array $box
     * @return array
     */
    public function getFitItems(array $items, array $box)
    {
        return $this->getPackerModel()->getFitItems($items, $box);
    }

    /**
     * Returns an array of database scheme
     * @return array
     */
    public function getDbScheme()
    {
        return array(
            'module_packer_boxes' => array(
                'fields' => array(
                    'name' => array('type' => 'varchar', 'length' => 255, 'not_null' => true),
                    'outer_width' => array('type' => 'int', 'length' => 10, 'not_null' => true),
                    'outer_length' => array('type' => 'int', 'length' => 10, 'not_null' => true),
                    'outer_depth' => array('type' => 'int', 'length' => 10, 'not_null' => true),
                    'empty_weight' => array('type' => 'int', 'length' => 10, 'not_null' => true),
                    'inner_width' => array('type' => 'int', 'length' => 10, 'not_null' => true),
                    'inner_length' => array('type' => 'int', 'length' => 10, 'not_null' => true),
                    'inner_depth' => array('type' => 'int', 'length' => 10, 'not_null' => true),
                    'max_weight' => array('type' => 'int', 'length' => 10, 'not_null' => true),
                    'status' => array('type' => 'int', 'length' => 1, 'not_null' => true, 'default' => 0),
                    'weight_unit' => array('type' => 'varchar', 'length' => 255, 'not_null' => true),
                    'size_unit' => array('type' => 'varchar', 'length' => 255, 'not_null' => true),
                    'box_id' => array('type' => 'int', 'length' => 10, 'auto_increment' => true, 'primary' => true),
                    'shipping_method' => array('type' => 'varchar', 'length' => 255, 'not_null' => true, 'default' => '')
                )
            )
        );
    }

    /**
     * Returns the Box model instance
     * @return \gplcart\modules\packer\models\Box
     */
    public function getBoxModel()
    {
        /** @var \gplcart\modules\packer\models\Box $instance */
        $instance = Container::get('gplcart\\modules\\packer\\models\\Box');
        return $instance;
    }

    /**
     * Returns the Packer model instance
     * @return \gplcart\modules\packer\models\Packer
     */
    public function getPackerModel()
    {
        /** @var \gplcart\modules\packer\models\Packer $instance */
        $instance = Container::get('gplcart\\modules\\packer\\models\\Packer');
        return $instance;
    }
}
