<?php

// ************************************************************************
//
// wbGallery
// Licensed under the GNU/GPL Open Source License
// (c) 2008 Webuddha.com, The Holodyn Corporation
//
// Image Management
//
// ************************************************************************

defined( '_VALID_MOS' ) or die( 'Restricted access' );

// ************************************************************************
class wbGallery_img {

  // ************************************************************************
  function manage() {
    global $my, $mainframe, $database, $option, $priTask, $subTask;
    global $WBG_CONFIG, $wbGalleryDB_cat;

    $limit      = intval( $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', $mainframe->getCfg('list_limit') ) );
    $limitstart = intval( $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 ) );
    $cat_id     = intval( $mainframe->getUserStateFromRequest( "view{$option}cat_id", 'cat_id', 0 ) );
    $view_mode  = trim( $mainframe->getUserStateFromRequest( "view{$option}view_mode", 'view_mode', 'image' ) );
    $searchkw   = trim( strtolower( $mainframe->getUserStateFromRequest( "view{$option}searchkw", 'searchkw', '' ) ) );

    $where = array();
    if($cat_id > 0)
      $where[] = 'i.cat_id = '.$cat_id;
    if($cat_id < 0)
      $where[] = 'i.cat_id = 0';
    if($searchkw)
      $where[] = "(i.name LIKE '%$searchkw%' OR i.sku LIKE '%$searchkw%')";

    // Get Total
    $database->setQuery("
      SELECT COUNT(DISTINCT i.id) AS total
      FROM #__wbgallery_img AS i
      LEFT JOIN #__wbgallery_cat AS c ON c.id = i.cat_id
      ".(count($where)?"WHERE ".join(" AND ", $where):'')."
      ");
    $total = $database->loadResult();
    echo $database->getErrorMsg();

    // Page Navigation
    require_once($mainframe->getCfg('absolute_path') . '/administrator/includes/pageNavigation.php');
    $pageNav = new mosPageNav($total, $limitstart, $limit);

    // Get Records
    $database->setQuery("
      SELECT i.*, c.name AS cat_name, c.title AS cat_title
      FROM #__wbgallery_img AS i
      LEFT JOIN #__wbgallery_cat AS c ON c.id = i.cat_id
      ".(count($where)?"WHERE ".join(" AND ", $where):'')."
      GROUP BY i.id
      ORDER BY c.name, i.ordering, i.name
      ".($limit?"LIMIT $limitstart, $limit":'')."
    ");
    $rows = $database->loadObjectList();
    echo $database->getErrorMsg();

    // Build Select Lists
    $lists    = Array();
    $catTree  = $wbGalleryDB_cat->getCategoryTree();
    $tList = Array(
      mosHTML::makeOption( '0', 'All Categories...', 'id', 'name' ),
      mosHTML::makeOption( '-1', 'No Category Images...', 'id', 'name' )
      );
    $tList = array_merge($tList,$catTree);
    $lists['cat_id'] = mosHTML::selectList($tList,'cat_id', 'onchange="document.adminForm.submit();"', 'id', 'name', $cat_id);
    $tList = Array(mosHTML::makeOption( '0', 'Select Destination...', 'id', 'name' ));
    $tList = array_merge($tList,$catTree);
    $lists['moveid'] = mosHTML::selectList($tList, 'moveid', '', 'id', 'name', null);

    if( $cat_id ){
      $lists['active_cat'] = new wbGalleryDB_cat($database);
      $lists['active_cat']->load($cat_id);
    }

    $tList = Array(
      mosHTML::makeOption( 'list', 'List', 'id', 'name' ),
      mosHTML::makeOption( 'image', 'Image', 'id', 'name' )
      );
    $lists['view_mode'] = mosHTML::selectList($tList, 'view_mode', 'onchange="document.adminForm.submit();"', 'id', 'name', $view_mode);
    $lists['view_mode_opt'] = $view_mode;
    $lists['searchkw'] = $searchkw;

    wbGallery_img_html::manage($rows, $pageNav, $lists);
  }

  // ************************************************************************
  function edit($id){
    global $my, $mainframe, $database, $option, $priTask, $subTask;
    global $WBG_CONFIG, $wbGalleryDB_cat;

    // Load Row
    $row = new wbGalleryDB_img($database);
    $row->load($id);

    // Build Select Lists
    $lists    = Array();
    $catTree  = $wbGalleryDB_cat->getCategoryTree();
    $tList = Array(mosHTML::makeOption( '0', 'Select Category...', 'id', 'name' ));
    $tList = array_merge($tList,$catTree);
    $lists['cat_id'] = mosHTML::selectList($tList,'cat_id', '', 'id', 'name', $row->cat_id);

    // Catchup...
    if( !$row->width || !$row->height ){
      $path = $mainframe->getCfg('absolute_path').$WBG_CONFIG->path_original.$row->file;
      if( file_exists($path) ){
        $row->size = filesize($path);
        $imgInfo = getimagesize($path);
        $row->width   = $imgInfo[0];
        $row->height  = $imgInfo[1];
        $row->store();
      }
    }

    wbGallery_img_html::edit($row, $lists);
  }

  // ************************************************************************
  function save(){
    global $my, $mainframe, $database, $option, $priTask, $subTask;
    global $WBG_CONFIG, $wbGalleryDB_cat;

    foreach( $_POST AS $k=>$v )
      $_POST[$k] = stripslashes($v);

    $row = new wbGalleryDB_img($database);
    $row->load(mosGetParam($_POST,'id',0));
    $row->bind($_POST);

    if(!$row->id){
      $row->ordering = 0;
      $row->created = date('Y-m-d H:i:s');
    } else
      $row->modified = date('Y-m-d H:i:s');

    // Is this an Ajax Call?
    if( in_array($subTask,Array('rename')) ){
      if(!$row->check() || !$row->store())
        echo 'Update Failed...';
        else echo 'Update Success...';
      exit();
    }

    if (!$row->check()) {
      echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
      exit();
    }
    if (!$row->store()) {
      echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
      exit();
    }
    $row->updateOrder('cat_id = '.(int)$row->cat_id);
    switch( $subTask ){
      case 'save':
        mosRedirect('index2.php?option='.$option.'&task=image', 'Changes Saved');
        break;
      case 'apply':
        mosRedirect('index2.php?option='.$option.'&task=image.edit&id=' . $row->id . '&hidemainmenu=1', 'Changes Saved');
        break;
    }

  }

  // ************************************************************************
  function remove($cid, $redirect = true){
    global $my, $mainframe, $database, $option, $priTask, $subTask;
    global $WBG_CONFIG, $wbGalleryDB_cat, $wbGallery_eng;

    $row = new wbGalleryDB_img($database);
    for($i=0,$n=count($cid);$i<$n;$i++){
      $id = intval($cid[$i]);
      $row->load($id);
      $wbGallery_eng->remove( $row->file );
      $cat_id = $row->cat_id;
      $row->delete();
      $row->updateOrder('cat_id = '.$cat_id);
    }

    if($redirect){
      mosRedirect('index2.php?option='.$option.'&task=image', 'Selected items have been removed');
    } else {
      return true;
    }
  }

  // ************************************************************************
  function move($cid, $redirect = true){
    global $my, $mainframe, $database, $option, $priTask, $subTask;
    global $WBG_CONFIG, $wbGalleryDB_cat;

    $row = new wbGalleryDB_img($database);
    $cat = new wbGalleryDB_cat($database);
    $moveid = mosGetParam( $_REQUEST, 'moveid', 0 );
    if( $moveid )
      $cat->load( $moveid );
    if( $moveid != $cat->id ){
      echo "<script> alert('The Destination Category is Not Valid'); </script>\n";
      return $this->manage();
    }
    if( !count($cid) ){
      echo "<script> alert('Please Select Image(s) to move.'); </script>\n";
      return $this->manage();
    }
    foreach( $cid AS $id ){
      $row->load( $id );
      $row->cat_id = $moveid;
      $row->store();
    }
    if($redirect){
      mosRedirect('index2.php?option='.$option.'&task=image', 'Selected items have been moved');
    } else {
      return true;
    }
  }

  // ************************************************************************
  function publish($cid, $published){
    global $my, $mainframe, $database, $option, $priTask, $subTask;
    global $WBG_CONFIG, $wbGalleryDB_cat;

    $row = new wbGalleryDB_img($database);
    for($i=0,$n=count($cid);$i<$n;$i++){
      $row->load($cid[$i]);
      $row->published = $published;
      if (!$row->store()) {
        echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
        exit();
      }
    }
    mosRedirect('index2.php?option='.$option.'&task=image');
  }

  // ************************************************************************
  function featured($cid, $featured){
    global $my, $mainframe, $database, $option, $priTask, $subTask;
    global $WBG_CONFIG, $wbGalleryDB_cat;

    $row = new wbGalleryDB_img($database);
    for($i=0,$n=count($cid);$i<$n;$i++){
      $row->load($cid[$i]);
      $row->featured = $featured;
      if (!$row->store()) {
        echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
        exit();
      }
    }
    mosRedirect('index2.php?option='.$option.'&task=image');
  }

  // ************************************************************************
  function reorder($id, $direction){
    global $my, $mainframe, $database, $option, $priTask, $subTask;
    global $WBG_CONFIG, $wbGalleryDB_cat;

    $row = new wbGalleryDB_img($database);
    $row->load($id);
    $row->move($direction, 'cat_id = '.(int)$row->cat_id);
    $row->updateOrder('cat_id = '.(int)$row->cat_id);
    mosRedirect('index2.php?option='.$option.'&task=image');
  }

  // ************************************************************************
  function order($cid){
    global $my, $mainframe, $database, $option, $priTask, $subTask;
    global $WBG_CONFIG, $wbGalleryDB_cat;

    $row = new wbGalleryDB_img($database);
    $conditions = array();
    $first = mosGetParam( $_POST, 'first', 0 );
    $order = mosGetParam( $_POST, 'order', Array(0) );
    if( preg_match('/\d+\,\d+/',$order) )
      $order = split(',',$order);
    if( $first > 0 ){
      $row->load( $first );
      $database->setQuery('
        SELECT i.*
        FROM #__wbgallery_img  AS i
        WHERE i.ordering < '.$ordering.'
        AND i.cat_id = '.$row->cat_id
      );
      $database->loadObject( $lowest_row );
      if( $lowest_row->id )
        $ordering = $lowest_row->ordering+1;
        else $ordering = 1;
      $res = true;
      foreach( $order AS $id ){
        $database->setQuery("
          UPDATE #__wbgallery_img AS i
          SET i.ordering='".$ordering++."'
          WHERE i.id= $id
          ");
        if( $database->query() != 1 )
          $res = false;
      }
      if( $res )
        echo "Order Updated...";
      else
        echo "Order Update Failed !!!";
    } else {
      for($i=0,$n=count($cid);$i<$n;$i++){
        $row->load($cid[$i]);
        if ($row->ordering != $order[$i]) {
          $row->ordering = $order[$i];
          if (!$row->store()) {
            echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
            exit();
          }
          $condition = 'cat_id = '.(int)$row->cat_id;
          $found = false;
          for($j=0,$k=count($conditions);$j<$k;$j++){
            $cond = $conditions[$j];
            if ($cond[1] == $condition) {
              $found = true;
              break;
            }
          }
          if (!$found){
            $conditions[] = Array($row->id, $condition);
          }
        }
      }
      for($i=0,$n=count($conditions);$i<$n;$i++){
        $condition = $conditions[$i];
        $row->load($condition[0]);
        $row->updateOrder($condition[1]);
      }
      mosRedirect('index2.php?option='.$option.'&task=image');
    }
    exit();
  }

  // ************************************************************************
  function upload(){
    global $my, $mainframe, $database, $option, $priTask, $subTask;
    global $WBG_CONFIG, $wbGalleryDB_cat;

    $cat_id     = intval( $mainframe->getUserStateFromRequest( "view{$option}cat_id", 'cat_id', 0 ) );

    $lists    = Array();
    $catTree  = $wbGalleryDB_cat->getCategoryTree();
    $tList = Array(mosHTML::makeOption( '0', 'Select a Category...', 'id', 'name' ));
    $tList = array_merge($tList,$catTree);
    $lists['cat_id'] = mosHTML::selectList($tList,'cat_id', '', 'id', 'name', $cat_id);

    wbGallery_img_html::upload($lists);
  }

  // ************************************************************************
  function upload_save($redirect = true){
    global $my, $mainframe, $database, $option, $priTask, $subTask;
    global $WBG_CONFIG, $wbGalleryDB_cat, $wbGallery_common, $wbGallery_eng;

    // Prepare Runtime
    $tempDir        = null;
    $known_images   = array('image/pjpeg', 'image/jpeg', 'image/jpg', 'image/png', 'image/gif');
    $time           = time();

    // Importing
    $importFolder   = mosGetParam($_REQUEST, 'folder', '');
    if( $importFolder && !file_exists($importFolder) ){
      echo "<script> alert('Import Folder Does Not Exist'); document.location.href='index2.php?option=" . $option . "&task=image.upload'; </script>\n";
      exit();
    }

    // Default Values
    $defRow = new wbGalleryDB_img( $database );
    $defRow->bind( $_POST );

    // Debug
    echo "Image Processing Start: ".$time.'<br/>';

    // ==============================v========================================
    // Single File Upload
    if(!empty($_FILES['img']['tmp_name'])){
      // Debug
      echo "Single File Detected <br/>";
      if(!in_array($_FILES['img']['type'], $known_images)){
        echo "<script> alert('Image type: ". $_FILES['img']['type']. " is an unknown type'); document.location.href='index2.php?option=" . $option . "&task=image.upload'; </script>\n";
        exit();
      }
      $wbGallery_eng->add($_FILES['img']['tmp_name'], $_FILES['img']['name'], $_FILES['img']['type'], $defRow);
      if($redirect)
        mosRedirect('index2.php?option='.$option.'&task=image.upload', 'Image Saved');

    }

    // ==============================v========================================
    // Zip File Upload
    if(!empty($_FILES['zip']['tmp_name'])){ //zip file upload

      // Debug
      echo "Compressed File Uploaded <br/>";

      // Create / Define Temporary Folder for Unzipped Files
      if(!mkdir($mainframe->getCfg('absolute_path') . '/media/' . $time)){
        if(!mkdir('/tmp/' . $time)){
          echo "<script> alert('Unable to Create Temp Directory'); history.back(); </script>\n";
          exit();
        } else {
          $tempDir = '/tmp/' . $time;
        }
      } else {
        $tempDir = $mainframe->getCfg('absolute_path') . '/media/' . $time;
      }

      // Uncompress ZIP or TAR.GZ
      if( preg_match('/zip$/i',$_FILES['zip']['name']) ){
        // Load ZIP functions
        require_once( $mainframe->getCfg('absolute_path') . '/administrator/includes/pcl/pclzip.lib.php' );
        require_once( $mainframe->getCfg('absolute_path') . '/administrator/includes/pcl/pclerror.lib.php' );
        $zipfile = new PclZip( $_FILES['zip']['tmp_name'] );
        if( substr(PHP_OS, 0, 3) == 'WIN' )
          define('OS_WINDOWS',1);
          else define('OS_WINDOWS',0);
        $ret = $zipfile->extract( PCLZIP_OPT_PATH, $tempDir );
        if($ret == 0){
          $wbGallery_common->remove_dir( $tempDir );
          echo "<script> alert('ZIP Extraction Error: ".$zipfile->errorName(true)."'); history.back(); </script>\n";
          exit();
        }
      } elseif( preg_match('/tar.gz$/i',$_FILES['zip']['name']) ) {
        // Load TAR functions
        require_once( $mainframe->getCfg('absolute_path') . '/includes/Archive/Tar.php' );
        $archive = new Archive_Tar( $_FILES['zip']['tmp_name'] );
        $archive->setErrorHandling( PEAR_ERROR_PRINT );
        if (!$archive->extractModify( $tempDir, '' )) {
          $wbGallery_common->remove_dir( $tempDir );
          echo "<script> alert('TAR Extraction Error'); history.back(); </script>\n";
          exit();
        }
      } else {
        // Unknown File...
        $wbGallery_common->remove_dir( $tempDir );
        echo "<script> alert('Unknown File Format - Must be .ZIP or .TAR.GZ'); history.back(); </script>\n";
        exit();
      }

    } // Zip File Upload

    // ==============================v========================================
    // Process Files from Folder
    if( $tempDir || $importFolder ){

      $processDirs    = Array();
      $files_added    = 0;
      $files_skipped  = 0;

      if( $tempDir )
        $processDirs[] = Array(
          'path' => $tempDir,
          'remove' => 1
          );
      if( $importFolder )
        $processDirs[] = Array(
          'path' => $importFolder,
          'remove' => 0
          );

      if( count($processDirs) ){
        foreach( $processDirs AS $procDir ){

          // Read Files from Temp Folder
          $regImg = Array();
          foreach( $known_images AS $k )
            $regImg[] = preg_replace('/^.*\//','',$k).'$';
          $regStr = '/'.join('|',$regImg).'/';
          $files = $wbGallery_common->find_files($procDir['path'],$regStr);
          unset($regImg);unset($regStr);

          // Debug
          echo "Unzipped ".count($files)." for processing <br/>";

          if( count($files) ){
            foreach( $files AS $file ){
              $filePath = $file['path'].'/'.$file['name'];
              $res = getimagesize( $filePath );
              $fileType = $res['mime'];
              if(in_array($fileType, $known_images))
                if( $wbGallery_eng->add($filePath, $file['name'], $fileType, $defRow) )
                  $files_added++;
                else $files_skipped++;
              else $files_skipped++;
            }
          }
          if( $procDir['remove'] )
            $wbGallery_common->remove_dir($procDir['path']);

        } // foreach processDirs
      } // if processDirs

      if($redirect)
        if( !$files_added && !$files_skipped )
          mosRedirect('index2.php?option='.$option.'&task=image.upload', 'Error: No Files were Processed or Error Finding Files');
        else
          mosRedirect('index2.php?option='.$option.'&task=image.upload', $files_added.' images added - '.$files_skipped.' files skipped');
      else
        if( !$files_added && !$files_skipped )
          return false;
        else
          return true;

    }

    mosRedirect('index2.php?option='.$option.'&task=image.upload', 'Please Specify Images for Processing');
    return false;
  }

}

class wbGallery_img_html {

  // ************************************************************************
  function manage($rows, $pageNav, $lists){
    global $my, $mainframe, $database, $option, $priTask, $subTask;
    global $WBG_CONFIG, $wbGalleryDB_cat;

    ?>
    <link rel="stylesheet" href="<?= $mainframe->getCfg('live_site') ?>/administrator/components/<?= $option ?>/css/image.manage.css" type="text/css" />
    <form action="index2.php" method="post" name="adminForm" id="adminForm">
      <table class="adminheading" cellpadding="0" cellspacing="0" width="100%">
        <tr>
          <th class="mediamanager" valign="top">
            <font size="+1">wbGallery</font><br/>
            Manage Collection Images<br/>
            <span id="ajax_result">
              <font color="red"><?= ( is_object($lists['active_cat']) ? ($lists['active_cat']->name?$lists['active_cat']->name:'No').' Category Images' : 'Viewing All Images...' ) ?></font><br/>
            </span>
          </th>
          <td valign="top" style="width:160px;">
            Select All: <input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count( $rows );?>);" /><br/>
            <?= $lists['moveid']; ?> <input type="submit" value=" Move Selected Items " onClick="document.adminForm.task.value='image.move';document.adminForm.submit();" /><br/>
          </td>
          <td valign="top" style="width:240px;">
            <table cellpadding="0" cellspacing="2">
              <tr>
                <td nowrap align="right">View Mode: </td>
                <td><?= $lists['view_mode']; ?></td>
              </tr>
              <tr>
                <td nowrap align="right">Filter Category: </td>
                <td><?= $lists['cat_id']; ?></td>
              </tr>
              <tr>
                <td nowrap align="right">Filter Term: </td>
                <td nowrap><input type="text" name="searchkw" value="<?= $lists['searchkw'] ?>" onchange="document.adminForm.submit();" />
                  <input type="submit" value=" GO " onClick="document.adminForm.submit();" /></td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
      <table class="adminlist"><tr><th colspan="3"><?= $pageNav->getPagesLinks(); ?></th></tr></table>
      <?php

        if( !count($rows) )
          echo '<h1 class="error_msg">There are No Images to View...</h1>';
        elseif( $lists['view_mode_opt'] == 'list' )
          wbGallery_img_html::manage_list( $rows, $pageNav );
        else
          wbGallery_img_html::manage_sort( $rows, $pageNav );

      ?>
      <input type="hidden" name="option" value="<?= $option; ?>" />
      <input type="hidden" name="task" value="image" />
      <input type="hidden" name="boxchecked" value="0" />
      <input type="hidden" name="hidemainmenu" value="0" />
      <?= $pageNav->getListFooter(); ?>
    </form>
    <?php
  }

  // ************************************************************************
  function manage_list($rows, $pageNav){
    global $my, $mainframe, $database, $option, $priTask, $subTask;
    global $WBG_CONFIG, $wbGalleryDB_cat, $wbGallery_common;

    ?>
    <script language="Javascript">
      function saveOrderX(n) {
        checAllX(n);
      }
      function checAllX( n ) {
        for ( var j = 0; j <= n; j++ ) {
          box = eval( "document.adminForm.cb" + j );
          if ( box ) {
            if ( box.checked == false ) {
              box.checked = true;
            }
          } else {
            alert("You cannot change the order of items, as an item in the list is `Checked Out`");
            return;
          }
        }
        submitform('image.order');
      }
    </script>
    <table class="adminlist">
      <tr>
        <th width="1%">#</th>
        <th width="1%">&nbsp;</th>
        <th width="5%">Image</th>
        <th width="30%">Name<br/>Sku</th>
        <th width="30%">Category<br/>cat_id</th>
        <th width="10%">Photographer<br/>Price</th>
        <th width="5%" nowrap># Hits<br/>&nbsp;</th>
        <th width="1%" colspan="2">Reorder<br/>&nbsp;</th>
        <th width="1%">Order<br/>&nbsp;</th>
        <th width="1%"><a href="javascript: saveOrderX(<?= count( $rows )-1 ?>)"><img src="images/filesave.png" border="0" width="16" height="16" alt="Save Order" /></a><br/>&nbsp;</th>
        <th width="1%">Published<br/>&nbsp;</th>
        <th width="1%">Featured<br/>&nbsp;</th>
        <th width="10%">Created<br/>Modified</th>
        <th width="1%">ID</th>
      </tr>
      <?php
      $k = 0;
      for ($i=0, $n=count( $rows ); $i < $n; $i++) {
        $row = &$rows[$i];
        $link = 'index2.php?option='. $option . '&task=image.edit&hidemainmenu=1&id='. $row->id;
        ?>
        <tr class="<?php echo "row$k"; ?>">
          <td><?= $pageNav->rowNumber( $i ) ?></td>
          <td><?= mosHTML::idBox($i, $row->id); ?></td>
          <td><a href="<?= $link ?>" class="image"><span style="background-image:url(<?=
            $mainframe->getCfg('live_site').$WBG_CONFIG->path_tack.$row->file ?>);"><img
            src="<?= $mainframe->getCfg('live_site').$WBG_CONFIG->path_tack.$row->file ?>" border="0" /></span></a></td>
          <td><a href="<?= $link ?>"><b><?= stripslashes( $row->name ) ?></b></a><br/><?= $row->sku ? $row->sku : '-' ?></td>
          <td><b><?= $row->cat_name ?></b><br/><?= $row->cat_title ?></td>
          <td><?= $row->photographer ? $row->photographer : '-' ?><br/><?= $wbGallery_common->formatPrice($row->price) ?></td>
          <td><b><?= $row->hits ?></b></td>
          <td><?= $pageNav->orderUpIcon( $i, ($row->cat_id == @$rows[$i-1]->cat_id), 'image.orderup' ) ?></td>
          <td><?= $pageNav->orderDownIcon( $i, $n, ($row->cat_id == @$rows[$i+1]->cat_id), 'image.orderdown' ) ?></td>
          <td colspan="2"><input type="text" name="order[]" size="5" value="<?= $row->ordering; ?>" class="text_area" style="text-align: center" /></td>
          <td><a href="javascript:void(0);" onclick="return listItemTask('cb<?= $i ?>','<?= $row->published ? 'image.unpublish' : 'image.publish'; ?>')"><img src="images/<?= $row->published ? "tick.png" : "publish_x.png"; ?>" border="0" /></a></td>
          <td><a href="javascript:void(0);" onclick="return listItemTask('cb<?= $i ?>','<?= $row->featured ? 'image.unfeature' : 'image.feature'; ?>')"><img src="images/<?= $row->featured ? "tick.png" : "publish_x.png"; ?>" border="0" /></a></td>
          <td><b><?= preg_replace('/^(.*)\s.*$/','$1',$row->created) ?></b><br/>
            <?= preg_replace('/^(.*)\s.*$/','$1',$row->modified) ?></td>
          <td><b><?= $row->id ?></b></td>
        </tr>
        <?php
        $k = 1 - $k;
      }
      ?>
    </table>
    <?php
  }

  // ************************************************************************
  function manage_sort($rows, $pageNav){
    global $my, $mainframe, $database, $option, $priTask, $subTask;
    global $WBG_CONFIG, $wbGalleryDB_cat;

    ?>
    <ul id="order">
      <?php
      $imgPath = $mainframe->getCfg('live_site');
      if( $WBG_CONFIG->save_tack )
        $imgPath .= $WBG_CONFIG->path_tack;
      elseif( $WBG_CONFIG->save_thumb )
        $imgPath .= $WBG_CONFIG->path_thumb;
      elseif( $WBG_CONFIG->save_medium )
        $imgPath .= $WBG_CONFIG->path_medium;
      elseif( $WBG_CONFIG->save_large )
        $imgPath .= $WBG_CONFIG->path_large;
      elseif( $WBG_CONFIG->save_original )
        $imgPath .= $WBG_CONFIG->path_original;
      else die('No Images Active!');
      $k = 0; $count = 0;
      foreach( $rows AS $row ){
        $link = 'index2.php?option='. $option . '&task=image.edit&hidemainmenu=1&id='. $row->id;
        $imgFile = $imgPath.$row->file;
        ?>
        <li id="orderitem_<?= $row->id; ?>" class="img_block" onMouseup="wbgImgOrder();">
          <div class="block"><div class="wrap">
            <div class="r_con cb"><?= mosHTML::idBox($count, $row->id); ?></div>
            <div class="r_con"><a href="javascript:void(0);" onclick="return listItemTask('cb<?= $count ?>','<?= $row->published ? 'image.unpublish' : 'image.publish'; ?>')"><img src="images/<?= $row->published ? "tick.png" : "publish_x.png"; ?>" border="0" /></a></div>
            <div class="cat"><?= ($row->cat_name?$row->cat_name:'No Category...') ?></div>
            <div class="image"><span style="background-image:url('<?= $imgFile ?>');"><img src="<?= $imgFile ?>" border="0" /></span></div>
            <div class="controls">
              <a href="javascript:void(0);" onClick="wbgImgEdit(<?= $row->id ?>);" alt="Edit"><img src="images/edit.png" border="0" title="Edit" /></a>
              <a href="javascript:void(0);" onClick="wbgImgDelete(<?= $row->id ?>);" alt="Delete"><img src="images/delete.png" border="0" title="Delete" /></a>
              <div># hits<br/><?= $row->hits ?></div>
            </div>
            <div class="title"><input type="text" value="<?= htmlspecialchars($row->name) ?>" onChange="wbgImgRename(<?= $row->id ?>,this);" /></div>
          </div></div>
        </li>
        <?php
        $count++;
        $k = 1 - $k;
      }
      ?>
    </ul>
    <script> var firstRowId='<?= $rows[0]->id ?>'; </script>
    <script type="text/javascript" src="<?= $mainframe->getCfg('live_site') ?>/administrator/components/<?= $option ?>/js/common.js" type="text/javascript"></script>
    <script type="text/javascript" src="<?= $mainframe->getCfg('live_site') ?>/administrator/components/<?= $option ?>/js/image.manage.sort.js" type="text/javascript"></script>
    <div style="clear:both;">&nbsp;</div>
    <?php
  }

  // ************************************************************************
  function edit( $row, $lists ) {
    global $my, $mainframe, $database, $option, $priTask, $subTask;
    global $WBG_CONFIG, $wbGalleryDB_cat;

    // Clean the Row for Form Use
    mosMakeHtmlSafe( $row, ENT_QUOTES, 'description' );

    ?>
    <script language="javascript" type="text/javascript">
    function submitbutton(pressbutton, section) {
      var form = document.adminForm;
      if (pressbutton == 'image.cancel') {
        submitform( pressbutton );
        return;
      }

      <?php getEditorContents( 'editor1', 'description' ) ; ?>
      submitform(pressbutton);
    }
    </script>

    <form action="index2.php" method="post" name="adminForm">
    <table class="adminheading">
      <tr>
        <th class="edit" valign="top">
          <font size="+1">wbGallery</font><br/>
          <?= $row->id ? 'Edit' : 'Add' ?> Collection Image<br/>
        </th>
      </tr>
    </table>

    <table class="adminform" cellpadding="5">
      <tr>
        <th colspan="2">Image Details</th>
      </tr>
      <tr>
        <td width="200" valign="top">
          <table>
            <tr>
              <td>Name:</td>
              <td><input type="text" name="name" class="text_area" size="30" value="<?= $row->name ?>" /></td>
            </tr>
            <tr>
              <td>Category:</td>
              <td><?= $lists['cat_id'] ?></td>
            </tr>
            <tr>
              <td>Sku:</td>
              <td><input type="text" name="sku" class="text_area" size="30" value="<?= $row->sku ?>" /></td>
            </tr>
            <tr>
              <td>Price:</td>
              <td><input type="text" name="price" class="text_area" size="30" value="<?= $row->price ?>" /></td>
            </tr>
            <tr>
              <td>Photographer:</td>
              <td><input type="text" name="photographer" class="text_area" size="30" value="<?= $row->photographer ?>" /></td>
            </tr>
            <tr>
              <td>Published:</td>
              <td><?php echo mosHTML::yesnoRadioList('published', '', $row->published); ?></td>
            </tr>
            <tr>
              <td>Image Specs:</td>
              <td><?= $row->width.' x '.$row->height.' - '.$row->size.'b' ?></td>
            </tr>
            <tr>
              <td colspan="2">
                <a href="<?= $mainframe->getCfg('live_site').$WBG_CONFIG->path_original.$row->file ?>" target="_blank">
                <img src="<?= $mainframe->getCfg('live_site').$WBG_CONFIG->path_thumb.$row->file ?>" alt="<?php echo $row->name; ?>" border="0" />
                </a>
                <ul>
                  <?= ($WBG_CONFIG->save_original?'<li><a href="'.$mainframe->getCfg('live_site').$WBG_CONFIG->path_original.$row->file.'" target="_blank">Original Image</a>':'') ?>
                  <?= ($WBG_CONFIG->save_large?'<li><a href="'.$mainframe->getCfg('live_site').$WBG_CONFIG->path_large.$row->file.'" target="_blank">Large Image</a>':'') ?>
                  <?= ($WBG_CONFIG->save_medium?'<li><a href="'.$mainframe->getCfg('live_site').$WBG_CONFIG->path_medium.$row->file.'" target="_blank">Medium Image</a>':'') ?>
                  <?= ($WBG_CONFIG->save_thumb?'<li><a href="'.$mainframe->getCfg('live_site').$WBG_CONFIG->path_thumb.$row->file.'" target="_blank">Thumbnail Image</a>':'') ?>
                  <?= ($WBG_CONFIG->save_tack?'<li><a href="'.$mainframe->getCfg('live_site').$WBG_CONFIG->path_tack.$row->file.'" target="_blank">Thumbtack Image</a>':'') ?>
                </ul>
              </td>
            </tr>
          </table>
        </td>
        <td valign="top">
          <table height="400">
            <tr valign="top">
              <td>Description:</td>
            </tr>
            <tr valign="top">
              <td><?php editorArea( 'editor1',  $row->description , 'description', '100%', '100%', '60', '20' ) ; ?></td>
            </tr>
          </table>
        </td>
      </tr>
    </table>

    <input type="hidden" name="id" value="<?= $row->id; ?>" />
    <input type="hidden" name="option" value="<?= $option ?>" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="hidemainmenu" value="0" />
    </form>
    <?php
  }

  // ************************************************************************
  function upload($lists){
    global $my, $mainframe, $database, $option, $priTask, $subTask;
    global $WBG_CONFIG, $wbGalleryDB_cat;

    ?>
    <script language="javascript" type="text/javascript">
      function submitbutton(pressbutton, section) {
        var form = document.adminForm;
        if(pressbutton == 'image.cancel') {
          submitform( pressbutton );
          return;
        }
        <?php getEditorContents( 'editor1', 'description' ) ; ?>
        submitform(pressbutton);
      }
    </script>
    <form action="index2.php" method="post" name="adminForm" enctype="multipart/form-data">
      <table class="adminheading">
        <tr><th class="install" valign="top">
          <font size="+1">wbGallery</font><br/>
          Upload Collection Image(s)<br/>
          Max Upload( <?= ini_get('upload_max_filesize'); ?> )
          Max Exec( <?= ini_get('max_execution_time'); ?>s )
          Max Post( <?= ini_get('post_max_size'); ?> )
        </th></tr>
      </table>
      <table class="adminlist" width="100%">
        <tr>
          <th class="title" colspan="2">Select Upload Source</th>
        </tr>
        <tr>
          <td width="200">Single Image ( JPG, GIF, PNG ): </td>
          <td><input type="file" name="img" class="text_area" size="30" value="" /></td>
        </tr>
        <tr>
          <td>Compressed File ( ZIP, TAR.GZ ): </td>
          <td><input type="file" name="zip" class="text_area" size="30" value="" /></td>
        </tr>
        <tr>
          <td>Local Folder of Images: </td>
          <td><input type="name" name="folder" class="text_area" size="45" value="" /></td>
        </tr>
        <tr>
          <th class="title" colspan="2">Specify the Upload Default Details</th>
        </tr>
        <tr>
          <td>Default Category: </td>
          <td><?= $lists['cat_id'] ?></td>
        </tr>
        <tr>
          <td>Name:</td>
          <td><input type="text" name="name" class="text_area" size="30" value="" /></td>
        </tr>
        <tr>
          <td>Sku:</td>
          <td><input type="text" name="sku" class="text_area" size="30" value="" /></td>
        </tr>
        <tr>
          <td>Photographer:</td>
          <td><input type="text" name="photographer" class="text_area" size="30" value="" /></td>
        </tr>
        <tr>
          <td>Price:</td>
          <td><input type="text" name="price" class="text_area" size="30" value="" /></td>
        </tr>
        <tr>
          <td>Published: </td>
          <td><?= mosHTML::yesnoRadioList('published', '', 1) ?></td>
        </tr>
        <tr valign="top">
          <td>Description: </td>
          <td align="left"><?php
            // parameters : areaname, content, hidden field, width, height, rows, cols
            editorArea( 'editor1',  '' , 'description', '300px;', '200px;', '60', '20' ) ;
          ?></td>
        </tr>
      </table>
      <input type="hidden" name="option" value="<?= $option ?>" />
      <input type="hidden" name="task" value="" />
      <input type="hidden" name="boxchecked" value="0" />
      <input type="hidden" name="hidemainmenu" value="0" />
    </form>
    <?php
  }

}
