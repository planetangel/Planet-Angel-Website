<?php defined('_JEXEC') or die('Restricted access'); ?>

<script language="javascript" type="text/javascript">
var popUpWin=0;

function popUpWindow(URLStr, left, top, width, height) {
  if(popUpWin) {
	if(!popUpWin.closed) popUpWin.close();
  }
  popUpWinAttributes = 'toolbar=no,location=no,directories=no,status=no,menubar=no';
  popUpWinAttributes += ',scrollbars=no,resizable=no,copyhistory=yes,width='+width;
  popUpWinAttributes += ',height='+height+',left='+left+',top='+top+',screenX='+left+',screenY='+top;
  popUpWin = open(URLStr, 'popUpWin', popUpWinAttributes);
}
</script>

<?php $show_page_title = $this->imagebrowserConfig['show_page_title']; ?>
<?php if (!empty($this->imagebrowserConfig['page_title']) && $show_page_title == 1) { ?>
<div class="componentheading<?php echo $this->imagebrowserConfig['pageclass_sfx']; ?>" >
	<?php echo $this->imagebrowserConfig['page_title']; ?>
</div>
<?php } ?>

<?php $comp_description = $this->imagebrowserConfig['comp_description']; ?>
<?php $show_comp_description = $this->imagebrowserConfig['show_comp_description']; ?>
<?php if (!empty($comp_description) && $show_comp_description == 1) { ?>
	<div class="imagebrowser_description">
	<?php echo $comp_description; ?>
	</div>
<?php } ?>
        
<?php if (!empty($this->folder_title)) { ?>
<h3 class="folder_title"><?php echo $this->folder_title; ?></h3>
<?php } ?>
        
<?php if (is_array($this->subdirectories)) { ?>
	<?php foreach ($this->subdirectories as $directory) { ?>
		<?php 
		$href = 'index.php?option='.$this->option.'&view=gallery&folder=';
		if (!empty($this->folder)) {
			$href .= $this->folder.'/';
		}
		$href .= urlencode($directory);
		$href .= '&Itemid='.$_REQUEST['Itemid'];
		?>
       	<div class="dir">
        <a href="<?php echo JRoute::_($href); ?>">
			<?php echo $directory; ?>
		</a>
        </div>
	<?php } ?>
<?php } ?>
     
        
<?php if (is_array($this->images)) { ?>
	<?php foreach ($this->images as $image) { ?>
		<?php 
		if ($this->imagebrowserConfig['mode'] == 0) { // Popup mode
			$href = "<a href=\"Javascript:popUpWindow('components/com_imagebrowser/imagebrowser.popup.php?image=";
			$href .= $this->imagebrowserConfig['root_folder'].'/'.$this->folder.'/'.$image['file'];
			$href .= "', 0, 0, '".$image['img_size'][0]."', '";
            $href .= $image['img_size'][1]."') \">";
		}
		elseif ($this->imagebrowserConfig['mode'] == 1) { // Light Box mode
			$href = "<a href='".$this->imagebrowserConfig['root_folder']."/";
			if (!empty($this->folder)) {
				$href .= $this->folder."/";
			}
			$href .= $image['file'];
			$href .= "' rel='lightbox[".$this->folder."]' title='".$image['caption']."'>";
		}
        ?>
       	<div class="thumbnail" style="height:<?php echo $this->imagebrowserConfig['thumb_height']; ?>px; 
            						  width:<?php echo $this->imagebrowserConfig['thumb_width']; ?>px; 
                                          text-align:center;">
			<?php echo $href; // "<a>" opening tag ?>
				<img src="<?php echo $this->imagebrowserConfig['root_folder']; ?>/<?php echo $this->folder.'/thumb/'.$image['file']; ?>" 
                   	 class="thumbnail" alt="<?php echo $image['file']; ?>" border="0" />
			</a>
		</div>
<?php } ?>
<?php } ?>
        
        
<div style="clear:both;"></div>
<br />
<a href="Javascript:history.go(-1)">[ <?php echo _IMAGEBROWSER_LANG_BACK; ?> ]</a>





<?php //echo '<pre>'; var_dump($this); echo '</pre>'; //exit; ?>