<?php

use Geometry\Vec3i;
use Geometry\Vec2i;
use Geometry\Vec3f;

class Render
{
    private $image = null;
    private int $width = 0;
    private int $height = 0;
    private Vec3f $lightDir;

    public function __construct()
    {
        $this->lightDir = new Vec3f(0,0,0);
    }

    public function createView($width, $height)
    {
        $this->width = $width;
        $this->height = $height;
        $this->image = ImageCreateTrueColor($width, $height);
        ImageColorAllocate($this->image, 0, 0, 0);
    }

    /**
     * @param int $r
     * @param int $g
     * @param int $b
     */
    public function setBackGroundColor(int $r, int $g, int $b)
    {
        if ($r > 255 || $g > 255 || $b > 255) {
            echo 'Цветовая компонента не может превышать значение 255';

            return;
        }
        imagefill($this->image, 0, 0, ImageColorAllocate($this->image, $r, $g, $b));
    }

    /**
     * @param Vec3f $v
     */
    public function setLightDir(Vec3f $v)
    {
        $this->lightDir = $v;
    }

    public function renderModel(Model $model)
    {
        for ($i = 0; $i < $model->nfaces(); $i++) {
            $face = $model->face($i);
            if (!$face) die("Undefinded face # {$i} for model " . $model->getPath());
            $screenCoords = new SplFixedArray(3);
            /** @var Vec3f[] $worldCoords */
            $worldCoords = new SplFixedArray(3);

            for ($j = 0; $j < 3; $j++) {
                $v = $model->vert($face[$j]);
                $screenCoords[$j] = new Vec2i(($v->x + 1) * $this->width / 2, ($v->y + 1) * $this->height / 2);
                $worldCoords[$j] = $v;
            }

            $normal = $worldCoords[2]->sub($worldCoords[0])->mulVec($worldCoords[1]->sub($worldCoords[0]));
            $normal->normalize();
            $intensity = $normal->scalar($this->lightDir);
            if ($intensity > 0) {
                $color = ImageColorAllocate($this->image, $intensity * 255, $intensity * 255, $intensity * 255);
                Render::triangle($screenCoords[0], $screenCoords[1], $screenCoords[2], $this->image, $color);
            }
        }

        imageflip($this->image, IMG_FLIP_VERTICAL);
        ImagePng($this->image, 'out.png');
    }

    public static function line2d(Vec3i $v1, Vec3i $v2, $img, $color)
    {
        $v1 = clone $v1;
        $v2 = clone $v2;
        $steep = false;
        if (abs($v1->x - $v2->x) < abs($v1->y - $v2->y)) {
            Helper::swap($v1->x, $v1->y);
            Helper::swap($v2->x, $v2->y);
            $steep = true;
        }

        if ($v1->x > $v2->x) {
            Helper::swap($v1->x, $v2->x);
            Helper::swap($v1->y, $v2->y);
        }

        $dx = $v2->x - $v1->x;
        $dy = $v2->y - $v1->y;
        $derror = abs($dy) * 2;
        $error = 0;
        $y = $v1->y;

        for ($x = $v1->x; $x <= $v2->x; $x++) {
            if ($steep) {
                imagesetpixel($img, $y, $x, $color);
            } else {
                imagesetpixel($img, $x, $y, $color);
            }

            $error += $derror;

            if ($error > $dx) {
                $y += ($v2->y > $v1->y ? 1 : -1);
                $error -= $dx * 2;
            }
        }
    }

    public static function triangle(Vec2i $v1, Vec2i $v2, Vec2i $v3, $img, $color)
    {
        if ($v1->y == $v2->y && $v2->y == $v3->y) return;

        $v1 = clone $v1;
        $v2 = clone $v2;
        $v3 = clone $v3;

        if ($v1->y > $v2->y) Helper::swap($v1, $v2);
        if ($v1->y > $v3->y) Helper::swap($v1, $v3);
        if ($v2->y > $v3->y) Helper::swap($v2, $v3);

        $totalHeight = $v3->y - $v1->y;

        for ($i = 0; $i < $totalHeight; $i++) {
            $secondHalf = $i > $v2->y - $v1->y || $v1->y == $v2->y;
            $segmentHeight = $secondHalf ? $v3->y - $v2->y : $v2->y - $v1->y;
            $alpha = $i / $totalHeight;
            $beta = ($i - ($secondHalf ? $v2->y - $v1->y : 0)) / $segmentHeight;

            $A = $v1->add($v3->sub($v1)->mulVal($alpha));
            $B = $secondHalf ? $v2->add($v3->sub($v2)->mulVal($beta)) : $v1->add($v2->sub($v1)->mulVal($beta));

            if ($A->x > $B->x) Helper::swap($A, $B);

            for ($j = $A->x; $j <= $B->x; $j++) {
                $tmp = $v1->y + $i;
                imagesetpixel($img, $j, $tmp , $color);
            }
        }
    }

}