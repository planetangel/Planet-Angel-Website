<?php

header("Content-Type: text/css");

$wpx = $_GET["albumwidth"]   . 'px';
$bpx = $_GET["thumbborder"]  . 'px';
$mpx = $_GET["thumbmargin"]  . 'px';
$ppx = $_GET["thumbpadding"] . 'px';
$aid = $_GET["albumid"];

// Planet Angel Edit
//echo "#gallery-flickr-$aid         { margin-top: -10px; }\n";

// Planet Angel Edit
//echo "#gallery-flickr-$aid ul      { list-style-type: none; width: $wpx; margin-left: 10px;}\n";
echo "#gallery-flickr-$aid ul      { list-style-type: none; margin: 0; padding: 0;}\n";

echo "#gallery-flickr-$aid ul li   { line-height: 0px; }\n";
echo "#gallery-flickr-$aid ul li a { float: left; padding: $ppx; margin: 0px $mpx $mpx 0px; }\n";

// Planet Angel Edit
echo "#gallery-flickr-$aid ul li a img { border: none; }\n";

echo "#gallery-flickr-$aid ul li a:hover { background: #ddd; }\n";
echo ".gallery_grey  ul li a { border: $bpx solid #ddd;    background: #eee;    padding: $ppx; }\n";
echo ".gallery_blue  ul li a { border: $bpx solid #333;    background: #00448D; padding: $ppx; }\n";
echo ".gallery_red   ul li a { border: $bpx solid #450003; background: #5C0002; padding: $ppx; }\n";
echo ".gallery_green ul li a { border: $bpx solid #384328; background: #516324; padding: $ppx; }\n";
echo ".gallery_black ul li a { border: $bpx solid #000;    background: #212121; padding: $ppx; }\n";
echo ".gallery_brown ul li a { border: $bpx solid #000;    background: #563714; padding: $ppx; }\n";
echo ".gallery_none  ul li a { border: none;              background: none;    padding: $ppx; }\n";

// Planet Angel Edit
//echo ".gallery-flickr-link  { clear: both; padding-left: 28px; }\n";
echo ".gallery-flickr-link  { clear: both; padding: 10px 0 10px 0;}\n";

echo ".gallery-flickr-title { padding-top: 8px; padding-bottom: 0px; }\n";

?>