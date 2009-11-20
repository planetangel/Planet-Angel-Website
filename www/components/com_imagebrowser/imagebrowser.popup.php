<?php
/**
* @package		Joomla
* @subpackage	com_imagebrowser
* @copyright	Copyright (C) 2008 E-NOISE.COM LIMITED. All rights reserved.
* @license		GNU/GPL.
* @author 		Luis Montero [e-noise.com]
* @version 		0.1.6
* Joomla! and com_imagebrowser are free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed they include or
* are derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
*/

if (empty($_GET['image'])) {
	echo 'ERROR: No file selected.';
	exit;
}

$site_abs_path = str_replace('/components/com_imagebrowser/imagebrowser.popup.php', '', $_SERVER['SCRIPT_FILENAME']);
$img_rel_path = $_GET['image'];
$img_abs_path = $site_abs_path.'/'.$img_rel_path;

$img_size = getimagesize($img_abs_path);
$img_width = $img_size[0];
$img_height = $img_size[1];

if ($img_width < 500) {
	$info_box_height = 50;
}
else {
	$info_box_height = 30;
}

$folder = substr($img_rel_path, 0, strrpos($img_rel_path, '/'));
$img_file = substr($img_rel_path, strrpos($img_rel_path, '/'));

$split_filename = explode('.', $img_file);
$filename = $split_filename[0];
$extension = $split_filename[1];

$info_file = $site_abs_path.'/'.$folder.'/'.$filename.'.txt';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title><?php echo $img_file; ?></title>

<script language="javascript" type="text/javascript">
function reservar(openerNewUrl) {
	window.opener.location.href = openerNewUrl;
	window.close();
}
</script>

<style type="text/css">
<!--
.popup_note {
position:relative; 
top:-<?php echo $info_box_height; ?>px; 
height:<?php echo $info_box_height; ?>px; 
z-index:999; 
padding:5px; 
background-color:#F0F0F0;
}
-->
</style>
</head>

<body style="margin:0; padding:0;">

<div style="position:relative; z-index:1;">
<a href="Javascript:window.close();">
<img src="../../<?php echo $img_rel_path; ?>" alt="<?php echo $img_file; ?>" border="0" />
</a>
</div>


<?php if (file_exists($info_file)) { ?>
	<div class="popup_note">
		<?php require_once($info_file); ?>
	</div>
<?php } ?>

</body>
</html>
