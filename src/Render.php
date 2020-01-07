<?php

use Geometry\Vec3i;
use Geometry\Vec2i;
use Geometry\Vec3f;
use Geometry\Vec3;
use Library\ZBuffer;

class Render
{
    private $image = null;
    private int $width = 0;
    private int $height = 0;
    private int $depth = 255;
    private Vec3f $lightDir;
    private ZBuffer $zBuffer;

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
        $this->zBuffer = new ZBuffer($width, $height);
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
                $screenCoords[$j] = new Vec3i(
                    ($v->x + 1) * $this->width / 2,
                    ($v->y + 1) * $this->height / 2,
                    ($v->z + 1) * $this->depth / 2
                );
                $worldCoords[$j] = $v;
            }

            $normal = $worldCoords[2]->sub($worldCoords[0])->mulVec($worldCoords[1]->sub($worldCoords[0]));
            $normal->normalize();
            $intensity = $normal->scalar($this->lightDir);
            if ($intensity > 0) {
                $color = $this->colorRGB($intensity * 255, $intensity * 255, $intensity * 255);
                Render::triangle($screenCoords[0], $screenCoords[1], $screenCoords[2], $this->image, $color, $this->zBuffer);
            }
        }

        imageflip($this->image, IMG_FLIP_VERTICAL);
        ImagePng($this->image, 'out.png');
    }

    public static function line2d(Vec2i $v1, Vec2i $v2, $img, $color)
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

    public static function triangle(Vec3i $v1, Vec3i $v2, Vec3i $v3, $img, $color, ZBuffer $zBuffer)
    {
        if ($v1->y == $v2->y && $v2->y == $v3->y) return;

        $v1 = clone $v1;
        $v2 = clone $v2;
        $v3 = clone $v3;

        if ($v1->y > $v2->y) Helper::swap($v1, $v2);
        if ($v1->y > $v3->y) Helper::swap($v1, $v3);
        if ($v2->y > $v3->y) Helper::swap($v2, $v3);

        $totalHeight = $v3->y - $v1->y;
        $P = new Vec3i();
        for ($i = 0; $i < $totalHeight; $i++) {
            $secondHalf = $i > $v2->y - $v1->y || $v1->y == $v2->y;
            $segmentHeight = $secondHalf ? $v3->y - $v2->y : $v2->y - $v1->y;
            $alpha = $i / $totalHeight;
            $beta = ($i - ($secondHalf ? $v2->y - $v1->y : 0)) / $segmentHeight;

            $A = $v1->add($v3->sub($v1)->mulVal($alpha));
            $B = $secondHalf ? $v2->add($v3->sub($v2)->mulVal($beta)) : $v1->add($v2->sub($v1)->mulVal($beta));

            if ($A->x > $B->x) Helper::swap($A, $B);

            for ($j = $A->x; $j <= $B->x; $j++) {
                $phi = $B->x == $A->x ? 1 : ($j - $A->x) / ($B->x - $A->x);
                $P = self::calcP($P, $A, $B, $phi);
                $P->x = $j; $P->y = $v1->y + $i;
                $zIdx = $P->x + $P->y * $zBuffer->getWidth();
                if ($zBuffer->get($zIdx) < $P->z) {
                    $zBuffer->set($zIdx, $P->z);
                    imagesetpixel($img, $P->x, $P->y, $color);
                }
            }
        }
    }

    /**
     * Optimized Calculation
     * @param Vec3 $P
     * @param Vec3 $A
     * @param Vec3 $B
     * @param $phi
     * @return Vec3
     */
    private static function calcP(Vec3 $P, Vec3 $A, Vec3 $B, $phi)
    {
        $P->x = $A->x + ($B->x - $A->x) * $phi;
        $P->y = $A->y + ($B->y - $A->y) * $phi;
        $P->z = $A->z + ($B->z - $A->z) * $phi;

        return $P;
    }

    /**
     * @param $hexcolor
     * @return false|int
     */
    public function colorHex($hexcolor)
    {
        list($r, $g, $b) = sscanf($hexcolor, "#%02x%02x%02x");
        return ImageColorAllocate($this->image, $r, $g, $b);
    }

    /**
     * @param $r
     * @param $g
     * @param $b
     * @return false|int
     */
    public function colorRGB($r, $g, $b)
    {
        return ImageColorAllocate($this->image, $r, $g, $b);
    }


    public static function rasterize(Vec2i $v0, Vec2i $v1, &$image, $color, &$yBuffer = [])
    {
        if ($v0->x > $v1->x) Helper::swap($v0, $v1);

        for ($x = $v0->x; $x <= $v1->x; $x++) {
            $t = ($x - $v0->x) / ($v1->x - $v0->x);
            $y = (int) ($v0->y * (1 - $t) + $v1->y * $t + .5);
            if ($yBuffer[$x] < $y) {
                $yBuffer[$x] = $y;
                for ($w = 0; $w < 16; $w++) {
                    imagesetpixel($image, $x, $w , $color);
                }
            }
        }
    }

}