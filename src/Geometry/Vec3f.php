<?php

namespace Geometry;

class Vec3f extends Vec3
{
    public function __construct($x = .0, $y = .0, $z = .0)
    {
        $this->x = floatval($x);
        $this->y = floatval($y);
        $this->z = floatval($z);
    }
}