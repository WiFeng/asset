<?php
require 'Image.php';

$img1 = new Image('example_1.jpg');
$img2 = new Image('example_2.jpg');

//调整图片大小
//$img1->resize(700, 300, Image::DRAWTYPE_SCALE_FILL, Image::FITPOS_RIGHT_BOTTOM, array(255, 0, 0, 0));
//$img1->save('a_111.jpg', true);
//$img1->output();

//裁减图片
$img2->crop(20, 20);
$img2->output();

?>