<?php
/**
@version 1.0: mod_S5_newsticker
Author: Shape 5 - Professional Template Community
Copyright 2008
Available for download at www.shape5.com
*/

// no direct access
defined('_JEXEC') or die('Restricted access');


$pretext		= $params->get( 'pretext', '' );
$tween_time		= $params->get( 'tween_time', '' );
$height		        = $params->get( 'height', '' );
$width   		= $params->get( 'width', '' );
$text1		= $params->get( 'text1', '' );
$text1line		= $params->get( 'text1line', '' );
$text1target		= $params->get( 'text1target', '' );
$text2		= $params->get( 'text2', '' );
$text2line		= $params->get( 'text2line', '' );
$text2target		= $params->get( 'text2target', '' );
$text3		= $params->get( 'text3', '' );
$text3line		= $params->get( 'text3line', '' );
$text3target		= $params->get( 'text3target', '' );
$text4		= $params->get( 'text4', '' );
$text4line		= $params->get( 'text4line', '' );
$text4target		= $params->get( 'text4target', '' );
$text5		= $params->get( 'text5', '' );
$text5line		= $params->get( 'text5line', '' );
$text5target		= $params->get( 'text5target', '' );
$text6		= $params->get( 'text6', '' );
$text6line		= $params->get( 'text6line', '' );
$text6target		= $params->get( 'text6target', '' );
$text7		= $params->get( 'text7', '' );
$text7line		= $params->get( 'text7line', '' );
$text7target		= $params->get( 'text7target', '' );
$text8		= $params->get( 'text8', '' );
$text8line		= $params->get( 'text8line', '' );
$text8target		= $params->get( 'text8target', '' );
$text9		= $params->get( 'text9', '' );
$text9line		= $params->get( 'text9line', '' );
$text9target		= $params->get( 'text9target', '' );
$text10		= $params->get( 'text10', '' );
$text10line		= $params->get( 'text10line', '' );
$text10target	= $params->get( 'text10target', '' );
$display_time   	= $params->get( 'display_time', '' );


$tween_time = $tween_time*1000;
$display_time = $display_time*1000;


$s5_newsticker_tween = $tween_time;
$s5_newsticker_display = $display_time;

?>

<?php if ($pretext != "") { ?>
<br />
<?php echo $pretext ?>
<br /><br />
<?php } ?>


<?php
$br = strtolower($_SERVER['HTTP_USER_AGENT']); // what browser.
if(ereg("msie 6", $br)) {
$iss_ie6 = "yes";
}
else {
$iss_ie6 = "no";
}
?>


<?php
$br = strtolower($_SERVER['HTTP_USER_AGENT']); // what browser.
if(ereg("msie 7", $br)) {
$iss_ie7 = "yes";
}
else {
$iss_ie7 = "no";
}
?>

<?php

echo "<script language=\"javascript\" type=\"text/javascript\" >var s5_newsticker_tween = ".$s5_newsticker_tween.";</script>
	<script language=\"javascript\" type=\"text/javascript\" >var s5_newsticker_display = ".$s5_newsticker_display.";</script>";
	?>



<div style="width:<?php echo $width ?>;height:<?php echo $height ?>; overflow:hidden;">
<?php if ($text1line != "") { ?>
<div id="text1" style="padding:0px; display:block; opacity:.0; <?php if ($iss_ie6 == "yes") { ?>filter: alpha(opacity=0); -moz-opacity: 0;<?php } ?> width:<?php echo $width ?>; overflow:hidden;">
<?php if ($text1line != "") { ?>
<?php echo $text1line ?>
<?php } ?>
</div>
<?php } ?>

<?php if ($text2line != "") { ?>
<div id="text2" style="padding:0px; display:none;  opacity:.0; <?php if ($iss_ie6 == "yes") { ?>filter: alpha(opacity=0); -moz-opacity: 0;<?php } ?> width:<?php echo $width ?>; overflow:hidden;">
<?php if ($text2line != "") { ?>
<?php echo $text2line ?>
<?php } ?>
</div>
<?php } ?>

<?php if ($text3line != "") { ?>
<div id="text3" style="padding:0px; display:none;  opacity:.0; <?php if ($iss_ie6 == "yes") { ?>filter: alpha(opacity=0); -moz-opacity: 0;<?php } ?> width:<?php echo $width ?>; overflow:hidden;">
<?php if ($text3line != "") { ?>
<?php echo $text3line ?>
<?php } ?>
</div>
<?php } ?>

<?php if ($text4line != "") { ?>
<div id="text4" style="padding:0px; display:none;  opacity:.0; <?php if ($iss_ie6 == "yes") { ?>filter: alpha(opacity=0); -moz-opacity: 0;<?php } ?> width:<?php echo $width ?>; overflow:hidden;">
<?php if ($text4line != "") { ?>
<?php echo $text4line ?>
<?php } ?>
</div>
<?php } ?>



<?php if ($text5line != "") { ?>
<div id="text5" style="padding:0px; display:none;  opacity:.0; <?php if ($iss_ie6 == "yes") { ?>filter: alpha(opacity=0); -moz-opacity: 0;<?php } ?> width:<?php echo $width ?>; overflow:hidden; ">
<?php if ($text5line != "") { ?>
<?php echo $text5line ?>
<?php } ?>
</div>
<?php } ?>



<?php if ($text6line!= "") { ?>
<div id="text6" style="padding:0px; display:none;  opacity:.0; <?php if ($iss_ie6 == "yes") { ?>filter: alpha(opacity=0); -moz-opacity: 0;<?php } ?> width:<?php echo $width ?>; overflow:hidden;">
<?php if ($text6line != "") { ?>
<?php echo $text6line ?>
<?php } ?>
</div>
<?php } ?>



<?php if ($text7line != "") { ?>
<div id="text7" style="padding:0px; display:none;  opacity:.0; <?php if ($iss_ie6 == "yes") { ?>filter: alpha(opacity=0); -moz-opacity: 0;<?php } ?> width:<?php echo $width ?>; overflow:hidden;">
<?php if ($text7line != "") { ?>
<?php echo $text7line ?>
<?php } ?>
</div>
<?php } ?>



<?php if ($text8line != "") { ?>
<div id="text8" style="padding:0px; display:none; opacity:.0; <?php if ($iss_ie6 == "yes") { ?>filter: alpha(opacity=0); -moz-opacity: 0;<?php } ?> width:<?php echo $width ?>; overflow:hidden;">
<?php if ($text8line != "") { ?>
<?php echo $text8line ?>
<?php } ?>
</div>
<?php } ?>


<?php if ($text9line != "") { ?>
<div id="text9" style="padding:0px; display:none;  opacity:.0; <?php if ($iss_ie6 == "yes") { ?>filter: alpha(opacity=0); -moz-opacity: 0;<?php } ?> width:<?php echo $width ?>; overflow:hidden;">
<?php if ($text9line != "") { ?>
<?php echo $text9line ?>
<?php } ?>
</div>
<?php } ?>


<?php if ($text10line != "") { ?>
<div id="text10" style="padding:0px; display:none;  opacity:.0; <?php if ($iss_ie6 == "yes") { ?>filter: alpha(opacity=0); -moz-opacity: 0;<?php } ?> width:<?php echo $width ?>; overflow:hidden;">
<?php if ($text10line != "") { ?>
<?php echo $text10line ?>
<?php } ?>
</div>
<?php } ?>
</div>


<script language="javascript" type="text/javascript" src="modules/mod_s5_newsticker/s5_newsticker/fader.js"></script>
<script language="javascript" type="text/javascript" src="modules/mod_s5_newsticker/s5_newsticker/timing.js"></script>