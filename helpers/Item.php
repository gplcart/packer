<?php

/**
 * @package Packer
 * @author Iurii Makukh <gplcart.software@gmail.com>
 * @copyright Copyright (c) 2018, Iurii Makukh <gplcart.software@gmail.com>
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPL-3.0-or-later
 */

namespace gplcart\modules\packer\helpers;

use DVDoug\BoxPacker\Item as ItemInterface;

/**
 * Item object used by Packer library
 */
class Item implements ItemInterface
{
    /**
     * Itam array data
     * @var array
     */
    private $item = array();

    /**
     * Item constructor.
     * @param array $item
     */
    public function __construct(array $item)
    {
        $this->item = $item;
    }

    /**
     * @return array
     */
    public function getItemData()
    {
        return $this->item;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->item['title'];
    }

    /**
     * @return int
     */
    public function getWidth()
    {
        return $this->item['width'];
    }

    /**
     * @return int
     */
    public function getLength()
    {
        return $this->item['length'];
    }

    /**
     * @return int
     */
    public function getDepth()
    {
        return $this->item['width'];
    }

    /**
     * @return int
     */
    public function getWeight()
    {
        return $this->item['weight'];
    }

    /**
     * @return int
     */
    public function getVolume()
    {
        return $this->item['volume'];
    }

    /**
     * @return int
     */
    public function getKeepFlat()
    {
        return (int) !empty($this->item['flat']);
    }
}