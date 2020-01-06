<?php

namespace Geometry;

class Vec2
{
    public $x = 0;
    public $y = 0;

    public function __construct($x = 0, $y = 0)
    {
        $this->x = $x;
        $this->y = $y;
    }

    /**
     * @param Vec2 $v
     * @return static
     */
    public function add(Vec2 $v)
    {
        return new static(
            $this->x + $v->x,
            $this->y + $v->y
        );
    }

    /**
     * @param $val
     * @return static
     */
    public function mulVal($val)
    {
        return new static(
            $this->x * $val,
            $this->y * $val
        );
    }

    /**
     * @param Vec2 $v
     * @return static
     */
    public function sub(Vec2 $v)
    {
        return new static(
            $this->x - $v->x,
            $this->y - $v->y
        );
    }
}