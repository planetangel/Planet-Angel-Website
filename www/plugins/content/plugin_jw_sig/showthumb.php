<?php
/*
// "Simple Image Gallery" (in content items) Plugin for Joomla 1.0.x - Version 1.2.1
// License: http://www.gnu.org/copyleft/gpl.html
// Authors: Fotis Evangelou - George Chouliaras
// Copyright (c) 2006 JoomlaWorks.gr - http://www.joomlaworks.gr
// Project page at http://www.joomlaworks.gr - Demos at http://demo.joomlaworks.gr
// ***Last update: January 6th, 2007***
*/

if($_GET['img'] == "")
exit;

$_GET['img'] = str_replace( '..', '', urldecode( $_GET['img'] ) );
$_image_ = '../../../images/stories/'.$_GET['img'];

$_width_min_ = intval($_GET['width']);
$_height_min_ = intval($_GET['height']);
$_quality_ = intval($_GET['quality']);

$new_w = $_width_min_;
$imagedata = getimagesize($_image_);

if(!$imagedata[0])
exit();

$new_h = (int)($imagedata[1]*($new_w/$imagedata[0]));

if(($_height_min_) AND ($new_h > $_height_min_)) {
$new_h = $_height_min_;
$new_w = (int)($imagedata[0]*($new_h/$imagedata[1]));
}

if(strtolower(substr($_GET['img'],-3)) == "jpg") {
header("Content-type: image/jpg");
$dst_img=ImageCreate($new_w,$new_h);
$src_img=ImageCreateFromJpeg($_image_);
$dst_img = imagecreatetruecolor($new_w, $new_h);
imagecopyresampled($dst_img,$src_img,0,0,0,0,$new_w,$new_h,ImageSX($src_img),ImageSY($src_img));
$img = Imagejpeg($dst_img,'', $_quality_);
}

if(substr($_GET['img'],-3) == "gif") {
header("Content-type: image/gif");
$dst_img=ImageCreate($new_w,$new_h);
$src_img=ImageCreateFromGif($_image_);  
ImagePaletteCopy($dst_img,$src_img);
ImageCopyResized($dst_img,$src_img,0,0,0,0,$new_w,$new_h,ImageSX($src_img),ImageSY($src_img));
$img = Imagegif($dst_img,'', $_quality_);
}

if(substr($_GET['img'],-3) == "png") {
header("Content-type: image/png");
$src_img=ImageCreateFromPng($_image_);
$dst_img = imagecreatetruecolor($new_w, $new_h); 
ImagePaletteCopy($dst_img,$src_img);
ImageCopyResized($dst_img,$src_img,0,0,0,0,$new_w,$new_h,ImageSX($src_img),ImageSY($src_img));
$img = Imagepng($dst_img,'', $_quality_);
}

?>
