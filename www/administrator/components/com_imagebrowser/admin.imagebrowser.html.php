<?php
/**
* @package		Joomla
* @subpackage	com_imagebrowser
* @copyright	Copyright (C) 2008 E-NOISE.COM LIMITED. All rights reserved.
* @license		GNU/GPL.
* @author 		Luis Montero [e-noise.com]
* @version 		0.1.7b
* Joomla! and com_imagebrowser are free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed they include or
* are derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
*/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );

class HTML_imagebrowser {
	
	function attachCSS() {
		$document =& JFactory::getDocument();
		// Attach style sheet
		$document->addStyleSheet('components/com_imagebrowser/styles.css');
	}
	
	function attachPopupScripts() {
		?>
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
		<?php
	}
	
	function displayImagebrowser( &$data ) {		
		HTML_imagebrowser::attachPopupScripts();
		HTML_imagebrowser::attachCSS();
		
		JHTML::_('behavior.tooltip');
 
		// Set toolbar items for the page
		JToolBarHelper::title( JText::_( 'Image Browser Manager' ), 'generic.png' );
		JToolBarHelper::preferences( 'com_imagebrowser', '360' );
		//JToolBarHelper::help( 'screen.imagebrowser' );
		?>
        
		<div style="border:1px solid #CCCCCC; margin:0px 5px 10px 5px;">
		<script type="text/javascript">
			function submitnewfolder() {
				var form = document.new_folder_form;
				var myRegxp = /^([\sa-zA-Z0-9_-]+)$/;
				
				if (myRegxp.test(form.new_folder.value) == false) {
					alert( '<?php echo JText::_(_IMAGEBROWSER_FOLDER_NAME_NOT_VALID, true); ?>' );
				}
				else if (form.new_folder.value == "") {
					alert( '<?php echo JText::_(_IMAGEBROWSER_FOLDER_NAME_NOT_VALID, true); ?>' );
				}
				else {
					form.submit();
				}
			}
		</script>
        <form name="new_folder_form" action="index.php" method="post">
        <table cellpadding="3" cellspacing="1" border="0">
        	<tr>
            	<td><img src="components/com_imagebrowser/images/folder_new.png" alt="New Folder" /></td>
                <td><?php echo _IMAGEBROWSER_NEW_FOLDER; ?>:</td>
                <td><input type="text" name="new_folder" size="" /></td>
                <td><input type="button" name="submitbutton" onclick="Javascript:submitnewfolder();" value="<?php echo _IMAGEBROWSER_CREATE; ?>" /></td>
            </tr>
        </table>
        <input type="hidden" name="folder" value="<?php echo $data->current_folder; ?>" />
        <input type="hidden" name="task" value="new_folder" />
        <input type="hidden" name="option" value="<?php echo $data->option; ?>" />
        </form>
        
        <form action="index.php" method="post" enctype="multipart/form-data">
        <table cellpadding="3" cellspacing="1" border="0">
            <tr>
            	<td><img src="components/com_imagebrowser/images/up.png" alt="Upload File" /></td>
                <td><?php echo _IMAGEBROWSER_UPLOAD_IMAGE; ?>:</td>
                <td><input type="file" name="upload_file" size="" /></td>
                <td><input type="submit" name="submit" value="<?php echo _IMAGEBROWSER_UPLOAD; ?>" /></td>
            </tr>
        </table>
        <input type="hidden" name="folder" value="<?php echo $data->current_folder; ?>" />
        <input type="hidden" name="task" value="upload_file" />
        <input type="hidden" name="option" value="<?php echo $data->option; ?>" />
        </form>
        
        <form action="index.php" method="post" enctype="multipart/form-data">
        <table cellpadding="3" cellspacing="1" border="0">
            <tr>
                <td><img src="components/com_imagebrowser/images/generate_thumb.png" alt="Generate Thumbs" /></td>
            	<td><?php echo _IMAGEBROWSER_GENERATE_FOLDER_THUMBS; ?>:</td>
                <td><input type="submit" name="submit" value="<?php echo _IMAGEBROWSER_GO; ?>" /></td>
            </tr>
        </table>
        <input type="hidden" name="folder" value="<?php echo $data->current_folder; ?>" />
        <input type="hidden" name="task" value="generate_folder_thumbs" />
        <input type="hidden" name="option" value="<?php echo $data->option; ?>" />
        </form>
        
        <form action="index.php" method="post" enctype="multipart/form-data">
        <table cellpadding="3" cellspacing="1" border="0">
            <tr>
                <td><img src="components/com_imagebrowser/images/resize.png" alt="Generate Thumbs" /></td>
            	<td><?php echo _IMAGEBROWSER_PROCESS_FORCE_MAX_DIMENSIONS; ?>:</td>
                <td><input type="submit" name="submit" value="<?php echo _IMAGEBROWSER_GO; ?>" /></td>
            </tr>
        </table>
        <input type="hidden" name="folder" value="<?php echo $data->current_folder; ?>" />
        <input type="hidden" name="task" value="resize_folder_images" />
        <input type="hidden" name="option" value="<?php echo $data->option; ?>" />
        </form>
        
        </div>
        
        <div style="padding:3px 0px 10px 0px;"><?php echo $data->breadcrumbs; ?></div>
        
		<?php if (is_array($data->subdirectories)) { ?>
        <table class="adminlist">
        <?php foreach ($data->subdirectories as $directory) { ?>
        	<?php 
			$href = 'index.php?option='.$data->option.'&folder=';
			if (!empty($data->current_folder)) {
				$href .= $data->current_folder.'/';
			}
			$href .= urlencode($directory);
			$href .= '&Itemid='.JRequest::getVar('Itemid');
			?>
            <tr>
            <td width="16"><img src="components/com_imagebrowser/images/folder.png" alt="Folder" /></td>
            <td>
            <a href="<?php echo JRoute::_($href); ?>">
				<?php echo $directory; ?>
			</a>
            </td>
            <td width="32">
            <a href="<?php echo JRoute::_('index.php?option='.$data->option.'&task=rename_folder_form&folder='.$data->current_folder.'&oldname='.$directory); ?>"><img src="components/com_imagebrowser/images/edit.png" alt="Rename" border="0" /></a>
            </td>
            <td width="32">
            <a href="<?php echo JRoute::_('index.php?option='.$data->option.'&task=delete_folder&folder='.$data->current_folder.'&delete_folder='.$directory); ?>"><img src="components/com_imagebrowser/images/remove.png" alt="Delete" border="0" /></a>
            </td>
            </tr>
        <?php } ?>
        </table>
        <?php } ?>
        
        <?php if (is_array($data->images)) { ?>
        <br />
        <table class="adminlist">
        	<thead>
            <tr>
            	<th></th>
                <th><?php echo _IMAGEBROWSER_FILE_NAME; ?></th>
                <th><?php echo _IMAGEBROWSER_CAPTION; ?></th>
                <th><?php echo _IMAGEBROWSER_FILESIZE; ?></th>
                <th><?php echo _IMAGEBROWSER_MODIFIED; ?></th>
                <th></th>
                <th></th>
                <th></th>
            </tr>
            </thead>
			<?php foreach ($data->images as $image) { ?>
                <?php 
                $href = "<a href=\"Javascript:popUpWindow('../components/com_imagebrowser/imagebrowser.popup.php?image=";
				$href .= $data->config['root_folder'].'/'.$data->current_folder.'/'.$image['file'];
				$href .= "', 0, 0, '".$image['img_size'][0]."', '";
            	$href .= $image['img_size'][1]."') \">";
				
				$img_src = '../'.$data->config['root_folder'].'/'.$data->current_folder.'/thumb/'.$image['file'];
				if (!file_exists($img_src)) {
					$img_src = 'components/com_imagebrowser/images/no_thumb.png';
				}
                ?>
                <tr>
                	<td width="<?php echo $data->config['thumb_width']; ?>">
                    	<?php echo $href; // "<a>" opening tag ?>
                        <img src="<?php echo $img_src; ?>" class="thumbnail" 
                        	 alt="<?php echo $image['file']; ?>" border="0" />
                    	</a>
                    </td>
                    <td><?php echo $image['file']; ?></td>
                	<td><?php echo $image['caption']; ?></td>
                    <td><?php echo $image['file_size']; ?></td>
                	<td><?php echo $image['date_modified']; ?></td>
                    <td>
                    	<a href="<?php echo JRoute::_('index.php?option='.$data->option.'&task=edit_caption&folder='.$data->current_folder.'&file='.$image['file']); ?>">
                       		<?php echo _IMAGEBROWSER_EDIT_CAPTION; ?>
                        </a>
                    </td>
                    <td>
                    	<a href="<?php echo JRoute::_('index.php?option='.$data->option.'&task=generate_thumb&folder='.$data->current_folder.'&file='.$image['file']); ?>">
                        	<?php echo _IMAGEBROWSER_GENERATE_THUMB; ?>
                         </a>
                    </td>
                    <td width="16">
                    	<a href="<?php echo JRoute::_('index.php?option='.$data->option.'&task=delete_file&folder='.$data->current_folder.'&file='.$image['file']); ?>">
                        	<img src="components/com_imagebrowser/images/remove.png" alt="<?php echo _IMAGEBROWSER_DELETE; ?>" border="0" />
                        </a>
                    </td>
                </tr>
            <?php } ?>
        </table>
        <?php } ?>
        
        <br />
        <a href="Javascript:history.go(-1)">[ <?php echo _IMAGEBROWSER_LANG_BACK; ?> ]</a>
		<?php
	}
	
	function editCaption($data, $file, $caption) {
		// Set toolbar items for the page
		JToolBarHelper::title( JText::_( 'Edit Caption' ), 'generic.png' );
		JToolBarHelper::back();
		?>
		<form action="index.php">
        	<textarea name="caption" cols="60" rows="10"><?php echo $caption; ?></textarea>
        	
        	<br />
        	
            <input type="submit" name="submit" value="<?php echo _IMAGEBROWSER_SAVE; ?>" />
            <input type="hidden" name="file" value="<?php echo $file; ?>" />
            <input type="hidden" name="folder" value="<?php echo $data->current_folder; ?>" />
            <input type="hidden" name="option" value="<?php echo $data->option; ?>" />
            <input type="hidden" name="task" value="save_caption" />
        </form>
		<?php
	}
	
	function renameFolderForm($dir_tree, $current_folder, $oldname) {
		// Set toolbar items for the page
		JToolBarHelper::title( JText::_( 'Rename folder' ), 'generic.png' );
		JToolBarHelper::back();
		?>
		<script type="text/javascript">
			function submitnewfolder() {
				var form = document.rename_folder_form;
				var myRegxp = /^([\sa-zA-Z0-9_-]+)$/;
				
				if (myRegxp.test(form.newname.value) == false) {
					alert( '<?php echo JText::_(_IMAGEBROWSER_FOLDER_NAME_NOT_VALID, true); ?>' );
				}
				else if (form.newname.value == "") {
					alert( '<?php echo JText::_(_IMAGEBROWSER_FOLDER_NAME_NOT_VALID, true); ?>' );
				}
				else {
					form.submit();
				}
			}
		</script>
		
		<form name="rename_folder_form" action="index.php">
			<select name="new_parent_folder">
				<option value="">/</option>
				<?php foreach ($dir_tree as $dir) : ?>
				<option value="<?php echo $dir; ?>" <?php if ($dir == $current_folder) echo 'selected'; ?>><?php echo $dir; ?></option>
				<?php endforeach;; ?>
			</select>
		
        	<input type="text" name="newname" value="<?php echo $oldname; ?>" />
            <button type="button" onclick="Javascript:submitnewfolder();"><?php echo _IMAGEBROWSER_RENAME; ?></button>
            <input type="hidden" name="oldname" value="<?php echo $oldname; ?>" />
            <input type="hidden" name="folder" value="<?php echo $current_folder; ?>" />
            <input type="hidden" name="option" value="com_imagebrowser" />
            <input type="hidden" name="task" value="rename_folder" />
        </form>
		<?php
	}
	
	/**
	 * This function shows the screen to add thumbs in content items
	 *
	 */
	function plugin(&$data, $e_name) {
		?>
		<script type="text/javascript" src="components/com_imagebrowser/js/popup-plugin.js"></script>
		<script type="text/javascript">		
		function close_iframe() {
			window.parent.document.getElementById('sbox-window').close();
		}
		</script>
		
		<div style="float: right">
			<button type="button" onclick="close_iframe();"><?php echo JText::_('Cancel') ?></button>
		</div>
		
		Click on thumbnail to add to content
		
		<?php if (!empty($data->folder_title)) { ?>
        <h3 class="folder_title"><?php echo $data->folder_title; ?></h3>
        <?php } ?>
        
		<?php if (is_array($data->subdirectories)) { ?>
        <?php foreach ($data->subdirectories as $directory) { ?>
        	<?php 
			$href = 'index.php?option='.$data->option.'&task=plugin&tmpl=component&e_name='.$e_name.'&folder=';
			if (!empty($data->current_folder)) {
				$href .= $data->current_folder.'/';
			}
			$href .= urlencode($directory);
			?>
            <div class="dir">
            <a href="<?php echo JRoute::_($href); ?>">
				<?php echo $directory; ?>
			</a>
            </div>
        <?php } ?>
        <?php } ?>
        
        <?php if (is_array($data->images)) { ?>
        <?php foreach ($data->images as $image) { ?>
			<?php 
			$full_url = $data->config['root_folder'].'/'.$data->current_folder.'/'.$image['file'];
			$thumb_url = $data->config['root_folder'].'/'.$data->current_folder.'/thumb/'.$image['file'];
			$href = '<a href="#" onclick="ImageBrowserPluginPopup.onok(\''.$thumb_url.'\', \''.$full_url.'\', \''.$image['img_size'][0].'\', \''.$image['img_size'][1].'\');close_iframe();">';
            ?>
        	<div style="height:<?php echo $data->config['thumb_height']/2; ?>px; 
            							  width:<?php echo $data->config['thumb_width']/2; ?>px; 
                                          text-align:center;
                                          float:left;
                                          margin:0px 3px 3px 0px;">
				<?php echo $href; // "<a>" opening tag ?>
					<img src="<?php echo '../'.$thumb_url; ?>" 
                    	 class="thumbnail" alt="<?php echo $image['file']; ?>" 
                    	 border="0" 
                    	 width="<?php echo $data->config['thumb_width']/2; ?>" 
                    	 height="<?php echo $data->config['thumb_height']/2; ?>" />
				</a>
			</div>
        <?php } ?>
        <?php } ?>
        
        
        <div style="clear:both;"></div>
        <br />
        <a href="Javascript:history.go(-1)">[ <?php echo _IMAGEBROWSER_LANG_BACK; ?> ]</a>
		
		<?php
	}
	
	function generateFolderThumbs(&$data) {
		HTML_imagebrowser::attachCSS();
		// Set toolbar items for the page
		JToolBarHelper::title( JText::_( _IMAGEBROWSER_GENERATE_THUMB ), 'generic.png' );
		JToolBarHelper::back();
		?>
		
		<script type="text/javascript">
		window.addEvent('domready', function() {

			var tips = new Tips();
			 
			/**
			 * reqState is a custom option, onRequest/onComplete can handle
			 * it like a property. Its used here to take track of the element
			 * for the specific Ajax requests.
			 * 
			 * We define options here because it saves code and all
			 * Ajax instances have the same options. We also add a Tips::build
			 * to show the responseText
			 */
			 
			var options = {
				method: 'get',
				onRequest: function(){
					this.options.reqState
						.addClass('ajax-loading')
						.setHTML('Request ...');
				},
				onComplete: function(resp){
					this.options.reqState
						.removeClass('ajax-loading')
						.setProperty('title', 'Response: :: ' + resp)
						.setHTML('Complete!');
					tips.build(this.options.reqState);
			 
				}
			};
			 
			var xhrs = [
				<?php for ($i=0; $i < count($data->images); $i++) : ?>
				<?php if ($i>0) { echo ','; } ?>
				<?php $url = 'index.php?option='.$data->option.'&task=generate_thumb&folder='.$data->current_folder.'&file='.$data->images[$i]['file']; ?>
				new Ajax('<?php echo $url; ?>', $merge({reqState: $('req-state-<?php echo $i; ?>')}, options))
				<?php endfor; ?>
			];
			 
			/**
			 * The group, it has one Event that waits for all Ajax instances to finish
			 * it: onComplete. When all requests are complete this onComplete is fired.
			 */
			var group = new Group(<?php for ($i=0; $i < count($data->images); $i++) { if ($i>0) { echo ','; } echo 'xhrs['.$i.']'; } ?>);
			 
			group
				.addEvent('onRequest', function() {
					$('req-state-all')
						.addClass('ajax-loading')
						.setHTML('<?php echo JText::_(_IMAGEBROWSER_ALL_REQUESTS_STARTED, true); ?> ...');
				}).
				addEvent('onComplete', function() {
					$('req-state-all')
						.removeClass('ajax-loading')
						.setHTML('<?php echo JText::_(_IMAGEBROWSER_ALL_COMPLETED, true); ?>!');
				});
			 
			/**
			 * The event which starts the request.
			 */
			$('req-start').addEvent('click', function(e) {
				new Event(e).stop();
			 
				xhrs.each(function(xhr){
					xhr.request();
				});
			});
		});
		</script>
		
		<h3><?php echo _IMAGEBROWSER_GENERATE_FOLDER_THUMBS; ?>: <?php echo $data->current_folder; ?></h3>
		<a id="req-start" href="#"><?php echo _IMAGEBROWSER_START_THUMB_GENERATION; ?></a>
		<dl id="req-states">
			<?php for ($i=0; $i < count($data->images); $i++) : ?>
			<dt><?php echo $data->images[$i]['file']; ?>:</dt>
			<dd id="req-state-<?php echo $i; ?>">-</dd>
			<?php endfor; ?>
			
			<dt><strong><?php echo _IMAGEBROWSER_OVERALL; ?>:</strong></dt>
			<dd id="req-state-all">-</dd>
		</dl>
		
		<div><a href="Javascript:window.history.back();">[ <?php echo JText::_(_IMAGEBROWSER_LANG_BACK); ?> ]</a></div>
		<br />

		<?php
	}
	
	
	function resizeFolderImages(&$data) {
		HTML_imagebrowser::attachCSS();
		// Set toolbar items for the page
		JToolBarHelper::title( JText::_( _IMAGEBROWSER_FOLDER_RESIZE_IMAGES ), 'generic.png' );
		JToolBarHelper::back();
		?>
		
		<script type="text/javascript">
		window.addEvent('domready', function() {

			var tips = new Tips();
			 
			/**
			 * reqState is a custom option, onRequest/onComplete can handle
			 * it like a property. Its used here to take track of the element
			 * for the specific Ajax requests.
			 * 
			 * We define options here because it saves code and all
			 * Ajax instances have the same options. We also add a Tips::build
			 * to show the responseText
			 */
			 
			var options = {
				method: 'get',
				onRequest: function(){
					this.options.reqState
						.addClass('ajax-loading')
						.setHTML('Request ...');
				},
				onComplete: function(resp){
					this.options.reqState
						.removeClass('ajax-loading')
						.setProperty('title', 'Response: :: ' + resp)
						.setHTML('Complete!');
					tips.build(this.options.reqState);
			 
				}
			};
			 
			var xhrs = [
				<?php for ($i=0; $i < count($data->images); $i++) : ?>
				<?php if ($i>0) { echo ','; } ?>
				<?php $url = 'index.php?option='.$data->option.'&task=resize_images&folder='.$data->current_folder.'&file='.$data->images[$i]['file']; ?>
				new Ajax('<?php echo $url; ?>', $merge({reqState: $('req-state-<?php echo $i; ?>')}, options))
				<?php endfor; ?>
			];
			 
			/**
			 * The group, it has one Event that waits for all Ajax instances to finish
			 * it: onComplete. When all requests are complete this onComplete is fired.
			 */
			var group = new Group(<?php for ($i=0; $i < count($data->images); $i++) { if ($i>0) { echo ','; } echo 'xhrs['.$i.']'; } ?>);
			 
			group
				.addEvent('onRequest', function() {
					$('req-state-all')
						.addClass('ajax-loading')
						.setHTML('<?php echo JText::_(_IMAGEBROWSER_ALL_REQUESTS_STARTED, true); ?> ...');
				}).
				addEvent('onComplete', function() {
					$('req-state-all')
						.removeClass('ajax-loading')
						.setHTML('<?php echo JText::_(_IMAGEBROWSER_ALL_COMPLETED, true); ?>!');
				});
			 
			/**
			 * The event which starts the request.
			 */
			$('req-start').addEvent('click', function(e) {
				new Event(e).stop();
			 
				xhrs.each(function(xhr){
					xhr.request();
				});
			});
		});
		</script>
		
		<h3><?php echo _IMAGEBROWSER_FOLDER_RESIZE_IMAGES; ?>: <?php echo $data->current_folder; ?></h3>
		<a id="req-start" href="#"><?php echo _IMAGEBROWSER_START_IMAGE_RESIZING; ?></a>
		<dl id="req-states">
			<?php for ($i=0; $i < count($data->images); $i++) : ?>
			<dt><?php echo $data->images[$i]['file']; ?>:</dt>
			<dd id="req-state-<?php echo $i; ?>">-</dd>
			<?php endfor; ?>
			
			<dt><strong><?php echo _IMAGEBROWSER_OVERALL; ?>:</strong></dt>
			<dd id="req-state-all">-</dd>
		</dl>
		
		<div><a href="Javascript:window.history.back();">[ <?php echo JText::_(_IMAGEBROWSER_LANG_BACK); ?> ]</a></div>
		<br />

		<?php
	}

}
?>