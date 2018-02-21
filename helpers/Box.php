<?php

/**
 * @package Packer
 * @author Iurii Makukh <gplcart.software@gmail.com>
 * @copyright Copyright (c) 2018, Iurii Makukh <gplcart.software@gmail.com>
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPL-3.0-or-later
 */

namespace gplcart\modules\packer\helpers;

use DVDoug\BoxPacker\Box as BoxInterface;

/**
 * Box object used by Packer library
 */
class Box implements BoxInterface
{
    /**
     * Box data array
     * @var array
     */
    private $box;

    /**
     * Box constructor.
     * @param array $box
     */
    public function __construct(array $box)
    {
        $this->box = $box;
    }

    /**
     * @return array
     */
    public function getBoxData()
    {
        return $this->box;
    }

    /**
     * @return string
     */
    public function getReference()
    {
        return $this->box['name'];
    }

    /**
     * @return int
     */
    public function getOuterWidth()
    {
        return $this->box['outer_width'];
    }

    /**
     * @return int
     */
    public function getOuterLength()
    {
        return $this->box['outer_length'];
    }

    /**
     * @return int
     */
    public function getOuterDepth()
    {
        return $this->box['outer_depth'];
    }

    /**
     * @return int
     */
    public function getEmptyWeight()
    {
        return $this->box['empty_weight'];
    }

    /**
     * @return int
     */
    public function getInnerWidth()
    {
        return $this->box['inner_width'];
    }

    /**
     * @return int
     */
    public function getInnerLength()
    {
        return $this->box['inner_length'];
    }

    /**
     * @return int
     */
    public function getInnerDepth()
    {
        return $this->box['inner_depth'];
    }

    /**
     * @return int
     */
    public function getInnerVolume()
    {
        return $this->box['inner_volume'];
    }

    /**
     * @return int
     */
    public function getMaxWeight()
    {
        return $this->box['max_weight'];
    }
}