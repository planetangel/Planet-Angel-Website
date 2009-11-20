<?php

defined('_VALID_MOS') or die('Restricted access');

$WBG_CONFIG = new stdClass();
$WBG_CONFIG->save_large = '1';
$WBG_CONFIG->path_large = '/images/wbgallery/l/';
$WBG_CONFIG->width_large = '640';
$WBG_CONFIG->height_large = '853';
$WBG_CONFIG->quality_large = '80';
$WBG_CONFIG->save_medium = '1';
$WBG_CONFIG->path_medium = '/images/wbgallery/m/';
$WBG_CONFIG->width_medium = '320';
$WBG_CONFIG->height_medium = '427';
$WBG_CONFIG->quality_medium = '80';
$WBG_CONFIG->save_thumb = '1';
$WBG_CONFIG->path_thumb = '/images/wbgallery/t/';
$WBG_CONFIG->width_thumb = '180';
$WBG_CONFIG->height_thumb = '240';
$WBG_CONFIG->quality_thumb = '80';
$WBG_CONFIG->save_tack = '1';
$WBG_CONFIG->path_tack = '/images/wbgallery/k/';
$WBG_CONFIG->width_tack = '90';
$WBG_CONFIG->height_tack = '120';
$WBG_CONFIG->quality_tack = '80';
$WBG_CONFIG->save_original = '1';
$WBG_CONFIG->path_original = '/images/wbgallery/o/';
$WBG_CONFIG->use_memManager = '0';
$WBG_CONFIG->use_ImageMagik = '1';
$WBG_CONFIG->path_ImageMagik = '/usr/local/bin/';
$WBG_CONFIG->file_ImageMagik = 'mogrify';
$WBG_CONFIG->show_copyright = '1';
