<?php

namespace Geometry;

class Vec2i extends Vec2
{
    public $x = 0;
    public $y = 0;

    public function __construct($x = 0, $y = 0)
    {
        $this->x = intval($x);
        $this->y = intval($y);
    }
}