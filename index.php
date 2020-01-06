<?php
require __DIR__ . '/vendor/autoload.php';

use Geometry\Vec3f;

$start = microtime(true);

$render = new Render();
$render->createView(500, 500);
$render->setBackGroundColor(0, 50, 50);
$render->setLightDir(new Vec3f(0, 0, -1));

$model = new Model('data/head.obj');
$render->renderModel($model);

echo microtime(true) - $start;