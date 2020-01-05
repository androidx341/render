<?php


class Render
{
    private $image = null;

    public function __construct($width, $height)
    {
        $this->image = ImageCreate($width, $height);
    }

    private function getImage()
    {
        return $this->image;
    }

    public static function line2d(Vec3 $v1, Vec3 $v2, $img, $color)
    {
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

    public static function triangle(Vec3 $v1, Vec3 $v2, Vec3 $v3, $img, $color)
    {

    }

}