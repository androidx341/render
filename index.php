<?php
require __DIR__ . '/vendor/autoload.php';

use Geometry\Vec3f;

$start = microtime(true);

$render = new Render();
$render->createView(1200, 1200);
$render->setBackGroundColor(0, 0, 0);
$render->setLightDir(new Vec3f(-0.4, 0, -0.6));

$model = new Model('data/head.obj');
$render->renderModel($model);

echo microtime(true) - $start;