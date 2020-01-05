<?php
require __DIR__ . '/vendor/autoload.php';

$start = microtime(true);
$width = 600; //Ширина изображения
$height = 600; //Высота изображения

$img = ImageCreate($width, $height);// Создаем холст
$white = ImageColorAllocate($img, 255, 255, 255);//Задаем фоновый цвет
$black = ImageColorAllocate($img, 0, 0, 0);//Задаем цвет для линии окружности
$red = ImageColorAllocate($img, 100, 0, 0);//Еще один цвет
$blue = ImageColorAllocate($img, 0, 0, 100);//Еще один цвет
$green = ImageColorAllocate($img, 0, 100, 0);//Еще один цвет

$model = new Model('data/head.obj');

for ($i = 0; $i < $model->nfaces(); $i++) {
    $face = $model->face($i);
    if (!$face) {
        echo 'Undefinded face';
        die;
    }
    for ($j = 0; $j < 3; $j++) {
        $v0 = $model->vert($face[$j]);
        $v1 = $model->vert($face[($j + 1) % 3]);
        if (!$v0 || !$v1) {
            echo 'Undefinded vector';
            die;
        }
        $x0 = ($v0->x + 1) * $width / 2;
        $y0 = ($v0->y + 1) * $height / 2;
        $x1 = ($v1->x + 1) * $width / 2;
        $y1 = ($v1->y + 1) * $height / 2;
        Render::line2d(new Vec3($x0, $y0), new Vec3($x1, $y1), $img, $black);
    }
}

imageflip($img, IMG_FLIP_VERTICAL);
ImagePng($img, 'out.png');


echo microtime(true) - $start;