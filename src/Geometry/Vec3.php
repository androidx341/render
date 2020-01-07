<?php

namespace Geometry;

class Vec3
{
    public $x = 0;
    public $y = 0;
    public $z = 0;

    public function __construct($x = 0, $y = 0, $z = 0)
    {
        $this->x = $x;
        $this->y = $y;
        $this->z = $z;
    }

    /**
     * @param Vec3 $v
     * @return static
     */
    public static function fromVec(Vec3 $v)
    {
        return new static($v->x, $v->y, $v->z);
    }

    /**
     * @param Vec3 $v
     * @return static
     */
    public function add(Vec3 $v)
    {
        return new static(
            $this->x + $v->x,
            $this->y + $v->y,
            $this->z + $v->z,
        );
    }

    /**
     * @param $val
     * @param bool $self
     * @return static
     */
    public function mulVal($val, $self = false)
    {
        if ($self) {
            $this->x *= $val;
            $this->y *= $val;
            $this->z *= $val;

            return $this;
        }

        return new static(
            $this->x * $val,
            $this->y * $val,
            $this->z * $val,
        );
    }


    /**
     * @param Vec3 $v
     * @return static
     */
    public function mulVec(Vec3 $v)
    {
        return new static(
            $this->y * $v->z - $this->z * $v->y,
            $this->z * $v->x - $this->x * $v->z,
            $this->x * $v->y - $this->y * $v->x
        );
    }

    /**
     * @param Vec3 $v
     * @return static
     */
    public function sub(Vec3 $v)
    {
        return new static(
            $this->x - $v->x,
            $this->y - $v->y,
            $this->z - $v->z,
        );
    }

    /**
     * @return float
     */
    public function length()
    {
        return sqrt($this->x * $this->x + $this->y * $this->y + $this->z * $this->z);
    }

    /**
     * @return $this
     */
    public function normalize()
    {
        $length = $this->length();
        if ($length == 0) {
            return $this;
        }

        return $this->mulVal(1 / $length, true);
    }

    /**
     * @param Vec3 $v
     * @return float|int
     */
    public function scalar(Vec3 $v)
    {
        return $this->x * $v->x + $this->y * $v->y + $this->z * $v->z;
    }
}