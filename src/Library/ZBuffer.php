<?php


namespace Library;

use SplFixedArray;

class ZBuffer
{
    public array $zBuffer = [];
    private int $width = 0;
    private int $height = 0;
    private int $size = 0;


    public function __construct($width, $height)
    {
        $this->width = $width;
        $this->height = $height;
        $this->size = $width * $height;
        $this->flush();
    }

    public function flush()
    {
        $size = $this->width * $this->height;
        for ($i = 0; $i < $size; $i++) {
            $this->zBuffer[$i] = PHP_FLOAT_MIN;
        }
    }

    /**
     * @param $id
     * @return mixed
     */
    public function get($id)
    {
        if ($this->size < $id) {
            die('Wrong zbuffer position');
        }

        return $this->zBuffer[$id];
    }

    /**
     * @param $id
     * @param $value
     * @return mixed
     */
    public function set($id, $value)
    {
        if ($this->size < $id) {
            die('Wrong zbuffer position');
        }

        return $this->zBuffer[$id] = $value;
    }

    /**
     * @return int
     */
    public function getWidth(): int
    {
        return $this->width;
    }

    /**
     * @return int
     */
    public function getHeight(): int
    {
        return $this->height;
    }
}