<?php

/**
 * @package Packer
 * @author Iurii Makukh <gplcart.software@gmail.com>
 * @copyright Copyright (c) 2018, Iurii Makukh <gplcart.software@gmail.com>
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPL-3.0-or-later
 */

namespace gplcart\modules\packer\models;

use gplcart\core\Config;
use gplcart\core\interfaces\Crud;

/**
 * Manages basic behaviors and data related to Packer module
 */
class Box implements Crud
{

    /**
     * Database class instance
     * @var \gplcart\core\Database $db
     */
    protected $db;

    /**
     * Box constructor.
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->db = $config->getDb();
    }

    /**
     * Loads an box from the database
     * @param int $box_id
     * @return array
     */
    public function get($box_id)
    {
        return $this->db->fetch('SELECT * FROM module_packer_boxes WHERE box_id=?', array($box_id));
    }

    /**
     * Adds a new box
     * @param array $data
     * @return int
     */
    public function add(array $data)
    {
        return $this->db->insert('module_packer_boxes', $data);
    }

    /**
     * Deletes a box from the database
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        return (bool) $this->db->delete('module_packer_boxes', array('box_id' => $id));
    }

    /**
     * Updates a box
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, array $data)
    {
        return (bool) $this->db->update('module_packer_boxes', $data, array('box_id' => $id));
    }

    /**
     * Returns an array of boxes or counts them
     * @param array $options
     * @return array|integer
     */
    public function getList(array $options = array())
    {
        $sql = 'SELECT *';

        if (!empty($options['count'])) {
            $sql = 'SELECT COUNT(box_id)';
        }

        $sql .= ' FROM module_packer_boxes WHERE box_id IS NOT NULL';

        $conditions = array();

        if (isset($options['name'])) {
            $sql .= ' AND name=?';
            $conditions[] = $options['name'];
        }

        if (isset($options['status'])) {
            $sql .= ' AND status=?';
            $conditions[] = (int) $options['status'];
        }

        if (isset($options['shipping_method'])) {
            $sql .= ' AND shipping_method = ?';
            $conditions[] = $options['shipping_method'];
        }

        $allowed_order = array('asc', 'desc');

        $allowed_sort = array(
            'name', 'status', 'shipping_method', 'outer_width',
            'outer_length', 'outer_depth', 'empty_weight', 'inner_width',
            'inner_width', 'inner_length', 'inner_depth', 'max_weight',
            'weight_unit', 'size_unit'
        );

        if (isset($options['sort'])
            && in_array($options['sort'], $allowed_sort)
            && isset($options['order'])
            && in_array($options['order'], $allowed_order)) {
            $sql .= " ORDER BY {$options['sort']} {$options['order']}";
        } else {
            $sql .= ' ORDER BY box_id DESC';
        }

        if (!empty($options['limit'])) {
            $sql .= ' LIMIT ' . implode(',', array_map('intval', $options['limit']));
        }

        if (empty($options['count'])) {
            return $this->db->fetchAll($sql, $conditions, array('index' => 'box_id'));
        }

        return (int) $this->db->fetchColumn($sql, $conditions);
    }

    /**
     * Returns an array of matching boxes for the shipping method
     * @param string $shipping_method
     * @param bool $only_enabled
     * @return array
     */
    public function getListByShippingMethod($shipping_method, $only_enabled = true)
    {
        $boxes = array();

        foreach ((array) $this->getList(array('status' => $only_enabled ? true : null)) as $box) {

            if ($box['shipping_method'] === '') {
                $boxes[] = $box;
                continue;
            }

            // Glob-like matching. Check wildcards "*" and "?"
            $pattern = "#^" . strtr(preg_quote($shipping_method, '#'), array('\*' => '.*', '\?' => '.')) . "$#i";

            if (preg_match($pattern, $box['shipping_method']) === 1) {
                $boxes[] = $box;
            }
        }

        return $boxes;
    }
}
