<?php

namespace Geometry;

class Vec3i extends Vec3
{
    public $x = 0;
    public $y = 0;
    public $z = 0;

    public function __construct($x = 0, $y = 0, $z = 0)
    {
        $this->x = intval($x);
        $this->y = intval($y);
        $this->z = intval($z);
    }
}