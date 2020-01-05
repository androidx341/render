<?php


class Vec3
{
    public $x = .0;
    public $y = .0;
    public $z = .0;

    public function __construct($x = .0, $y = .0, $z = .0)
    {
        $this->x = floatval($x);
        $this->y = floatval($y);
        $this->z = floatval($z);
    }
}