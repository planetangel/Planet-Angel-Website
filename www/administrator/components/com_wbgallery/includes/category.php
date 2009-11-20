<?php

// ************************************************************************
//
// wbGallery
// Licensed under the GNU/GPL Open Source License
// (c) 2008 Webuddha.com, The Holodyn Corporation
//
// Category Management
//
// ************************************************************************

defined( '_VALID_MOS' ) or die( 'Restricted access' );

// ************************************************************************
class wbGallery_cat {

  // ************************************************************************
  function manage(){
    global $my, $mainframe, $database, $option, $priTask, $subTask;
    global $WBG_CONFIG, $wbGalleryDB_cat;

    $limit      = intval( $mainframe->getUserStateFromRequest( "viewlistlimit", 'limit', $mainframe->getCfg('list_limit') ) );
    $limitstart = intval( $mainframe->getUserStateFromRequest( "view{$option}limitstart", 'limitstart', 0 ) );
    $p_cid      = intval( $mainframe->getUserStateFromRequest( "view{$option}p_cid", 'p_cid', 0 ) );
    $search     = $mainframe->getUserStateFromRequest( "search{$option}", 'search', '' );
    $search     = $database->getEscaped( trim( strtolower( $search ) ) );

    $where = array();
    if(!empty($search))
      $where[] = "c.title LIKE '%" . $search . "%'";
    if($p_cid > 0)
      $where[] = 'c.parent_id = ' . $p_cid;

    $database->setQuery("
      SELECT COUNT(DISTINCT c.id)
      FROM #__wbgallery_cat AS c
      ".(count($where)?"WHERE ".join(" AND ", $where):'')."
      ");
    $total = $database->loadResult();
    echo $database->getErrorMsg();

    require_once($mainframe->getCfg('absolute_path') . '/administrator/includes/pageNavigation.php');
    $pageNav = new mosPageNav($total, $limitstart, $limit);

    $database->setQuery("
      SELECT c.*
        , COUNT(DISTINCT i.id) AS _images
        , COUNT(DISTINCT sc.id) AS _children
      FROM #__wbgallery_cat AS c
      LEFT JOIN #__wbgallery_img AS i ON i.cat_id = c.id
      LEFT JOIN #__wbgallery_cat AS sc ON sc.parent_id = c.id
      ".(count($where)?"WHERE ".join(" AND ", $where):'')."
      GROUP BY c.id
      ORDER BY c.parent_id, c.ordering, c.name
      ".($limit?"LIMIT $limitstart, $limit":'')."
      ");
    $rows = $database->loadObjectList();
    echo $database->getErrorMsg();

    // Sort Rows by Level
    $level = 0;
    $final = array();
    $tree  = array();
    for($i=0,$n=count($rows);$i<$n;$i++){
      $row = $rows[$i];
      $tree[$row->parent_id][] = $row;
    }
    $level = 0;
    for($i=0,$n=count($tree[$p_cid]);$i<$n;$i++){
      $row = $tree[$p_cid][$i];
      $wbGalleryDB_cat->recurseCategory($tree, $final, $row, $level);
    }
    if($limit)
      $final = array_slice($final, $limitstart, $limit);

    // Build Lists
    $lists = Array();
    $catTree  = $wbGalleryDB_cat->getCategoryTree();
    $tList = Array(mosHTML::makeOption( '0', 'No Parent...', 'id', 'name' ));
    $tList = array_merge($tList,$catTree);
    $lists['p_cid'] = mosHTML::selectList($tList,'p_cid', 'onchange="document.adminForm.submit();"', 'id', 'name', $p_cid);
    $lists['search'] = $search;

    wbGallery_cat_html::manage($final, $pageNav, $lists);
  }

  // ************************************************************************
  function edit($id){
    global $my, $mainframe, $database, $option, $priTask, $subTask;
    global $WBG_CONFIG, $wbGalleryDB_cat;

    $row = new wbGalleryDB_cat($database);
    $row->load($id);
    if(!$row->id)
      $row->published = 1;
    $lists    = Array();
    $catTree  = $wbGalleryDB_cat->getCategoryTree();
    $tList = Array(mosHTML::makeOption( '0', 'No Parent...', 'id', 'name' ));
    $tList = array_merge($tList,$catTree);
    $lists['parent_id'] = mosHTML::selectList($tList,'parent_id', '', 'id', 'name', (int)$row->parent_id);
    $lists['published'] = mosHTML::yesnoRadioList('published', '', (int)$row->published);
    $lists['access'] = mosAdminMenus::Access( $row );

    wbGallery_cat_html::edit($row, $lists);
  }

  // ************************************************************************
  function save($redirect = true){
    global $my, $mainframe, $database, $option, $priTask, $subTask;
    global $WBG_CONFIG, $wbGalleryDB_cat, $wbGallery_eng;

    // ==============================v========================================
    // Category Record
    $row = new wbGalleryDB_cat($database);
    $row->bind($_POST);
    if(!$row->id)
      $row->ordering = 999999999;
    if($row->parent_id == $row->id)
      $row->parent_id = 0;
    // clean title
    if( !$row->title ) $row->title = $row->name;
    $allowed = "/[^a-z0-9\\040\\.\\-\\_]/i"; // Letters, Digits, Hyphen, Underscore
    $row->title = preg_replace($allowed,' ',strtolower($row->title));
    $row->title = preg_replace('/\_+/','_',preg_replace('/\s+/','_',$row->title));
    // remove file if specified
    if( mosGetParam($_POST,'_delFile',null) == 'true' ) $row->file = null;
    $row->published = intval(mosGetParam($_REQUEST, 'published', 0));
    if (!$row->check()) {
      echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
      exit();
    }
    if (!$row->store()) {
      echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
      exit();
    }
    $row->updateOrder("parent_id = '".$row->parent_id."'");

    // ==============================v========================================
    // Category Image File
    if(!empty($_FILES['img']['tmp_name'])){
      if( $row->file )
        $wbGallery_eng->remove( $row->file );
      echo "Updating Category Image <br/>";
      $known_images = array('image/pjpeg', 'image/jpeg', 'image/jpg', 'image/png', 'image/gif');
      if(!in_array($_FILES['img']['type'], $known_images)){
        echo "<script> alert('Image type: ". $_FILES['img']['type']. " is an unknown type');
          document.location.href='index2.php?option=".$option."&task=category.edit&id=".$row->id."'; </script>\n";
        exit();
      }
      $imgData = $wbGallery_eng->add($_FILES['img']['tmp_name'], $_FILES['img']['name'], $_FILES['img']['type'], $row, false);
      if( $imgData->file ){
        $row->file = $imgData->file;
        if (!$row->store()) {
          echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
          exit();
        }
      } else {
        echo "<script> alert('Failed to Store Category Image');
          document.location.href='index2.php?option=".$option."&task=category.edit&id=".$row->id."'; </script>\n";
        exit();
      }
    }

    // ==============================v========================================
    if($redirect){
      switch($subTask){
        case 'save':
          mosRedirect('index2.php?option=' . $option . '&task=category&parent_id='.$row->parent_id, 'Changes to category saved');
          break;
        case 'apply':
          mosRedirect('index2.php?option=' . $option . '&task=category.edit&id='.$row->id . '&hidemainmenu=1', 'Changes to category saved');
          break;
      }
    } else {
      return $row->id;
    }
  }

  // ************************************************************************
  function remove($cid, $redirect = true){
    global $my, $mainframe, $database, $option, $priTask, $subTask;
    global $WBG_CONFIG, $wbGalleryDB_cat;

    $row = new wbGalleryDB_cat($database);
    for($i=0,$n=count($cid);$i<$n;$i++){
      $id = $cid[$i];
      // Check Images Exist
      $database->setQuery("
        SELECT COUNT(DISTINCT i.id)
        FROM #__wbgallery_img AS i
        WHERE i.cat_id = ".(int)$id);
      $total = $database->loadResult();
      if($total){
        echo "<script> alert('You Cannot Delete a Category with Existing Images'); window.history.go(-1); </script>\n";
        exit();
      }
      // Check Sub Categories Exist
      $database->setQuery("
        SELECT COUNT(DISTINCT c.id)
        FROM #__wbgallery_cat AS c
        WHERE c.parent_id = ".(int)$id);
      $total = $database->loadResult();
      if( $total ){
        echo "<script> alert('You Cannot Delete a Category with Existing Sub Categories'); window.history.go(-1); </script>\n";
        exit();
      }
      // Delete
      $row->delete($id);
    }
    if($redirect)
      mosRedirect('index2.php?option=' . $option . '&task=category', 'Selected categories removed');
    else
      return true;
  }

  // ************************************************************************
  function publish($cid, $published){
    global $my, $mainframe, $database, $option, $priTask, $subTask;
    global $WBG_CONFIG, $wbGalleryDB_cat;

    foreach( $cid AS $id ){
      $row = new wbGalleryDB_cat($database);
      $row->load($id);
      $row->published = $published;
      if (!$row->store()) {
        echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
        exit();
      }
    }
    mosRedirect('index2.php?option=' . $option . '&task=category');
  }

  // ************************************************************************
  function reorder($id, $direction){
    global $my, $mainframe, $database, $option, $priTask, $subTask;
    global $WBG_CONFIG, $wbGalleryDB_cat;

    $row = new wbGalleryDB_cat($database);
    $row->load($id);
    $row->move($direction,"parent_id = '".$row->parent_id."'");
    mosRedirect('index2.php?option=' . $option . '&task=category');
  }

  // ************************************************************************
  function order($cid){
    global $my, $mainframe, $database, $option, $priTask, $subTask;
    global $WBG_CONFIG, $wbGalleryDB_cat;

    $order = mosGetParam( $_POST, 'order', array(0) );
    $row = new wbGalleryDB_cat($database);
    $conditions = array();

    for($i=0,$n=count($cid);$i<$n;$i++){
      $row->load($cid[$i]);
      if ($row->ordering != $order[$i]) {
        $row->ordering = $order[$i];
        if (!$row->store()) {
          echo "<script> alert('".$database->getErrorMsg()."'); window.history.go(-1); </script>\n";
          exit();
        }
        $found = false;
        $condition = "parent_id = '".$row->parent_id."'";
        for($j=0,$k=count($conditions);$j<$k;$j++){
          $cond = $conditions[$j];
          if ($cond[1] == $condition){
            $found = true;
            break;
          }
        }
        if (!$found)
          $conditions[] = array ($row->id, $condition);
      }
    }
    for($i=0,$n=count($conditions);$i<$n;$i++){
      $condition = $conditions[$i];
      $row->load($condition[0]);
      $row->updateOrder($condition[1]);
    }
    mosRedirect('index2.php?option=' . $option . '&task=category');
  }

}

class wbGallery_cat_html {

  // ************************************************************************
  function manage($rows, $pageNav, $lists){
    global $my, $mainframe, $database, $option, $priTask, $subTask;
    global $WBG_CONFIG, $wbGalleryDB_cat;

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
        submitform('category.order');
      }
    </script>
    <form action="index2.php" method="post" name="adminForm">
      <table class="adminheading">
      <tr>
        <th class="categories" valign="top">
          <font size="+1">wbGallery</font><br/>
          Manage Collection Categories
        </th>
        <td nowrap="nowrap">Filter Category: <?= $lists['p_cid'] ?></td>
      </tr>
      </table>
      <table class="adminlist">
      <tr>
        <th width="5%" align="left">#</th>
        <th width="5%"><input type="checkbox" name="toggle" value="" onClick="checkAll(<?= count($rows) ?>);" /></th>
        <th width="60%">Category Name</th>
        <th width="5%" nowrap>Total Images</th>
        <th width="5%">Published</th>
        <th colspan="2" width="5%">Reorder</th>
        <th width="5%">Order</th>
        <th width="5%"><a href="javascript: saveOrderX(<?= count( $rows )-1 ?>)"><img src="images/filesave.png" border="0" width="16" height="16" alt="Save Order" /></a></th>
        <th width="5%" nowrap>Category ID</th>
      </tr>
      <?php

      // Ordering Calculations & Review
      $nChildren = $sChildren = Array('0'=>0);
      foreach($rows AS $row)if($row->parent_id==0)$nChildren[0]++;

      // Loop through Categories
      $k = 0;
      for ($i=0,$n=count($rows);$i<$n;$i++) {
        $row      = &$rows[$i];
        $link     = 'index2.php?option='. $option . '&task=category.edit&hidemainmenu=1&cid='. $row->id;
        $showLink = 'index2.php?option='. $option . '&task=image&cat_id='. $row->id;
        $checked  = mosCommonHTML::CheckedOutProcessing( $row, $i );
        $sChildren[$row->parent_id]++;
        if( !array_key_exists($row->id,$nChildren) )
          $nChildren[$row->id] = $row->_children;
        $orderOk    = (array_key_exists($row->parent_id,$nChildren) ? $nChildren[$row->parent_id] > 1 : 0);
        $orderHead  = ($sChildren[$row->parent_id] == 1) && ($nChildren[$row->parent_id] > 1);
        $orderTail  = ($sChildren[$row->parent_id] >= $nChildren[$row->parent_id]);
        ?>
        <tr class="<?php echo "row$k"; ?>">
          <td><?= $pageNav->rowNumber( $i ) ?></td>
          <td><?= $checked ?></td>
          <td><a href="<?= $link ?>"><?= stripslashes( $row->name ) .' ( '. stripslashes( $row->title ) .' )' ?> </a></td>
          <td align="center"><?= $row->_images ?> <a href="<?= $showLink ?>">[ show ]</a></td>
          <td align="center"><a href="javascript:void(0);" onclick="return listItemTask('cb<?= $i ?>','<?= $row->published ? 'category.unpublish' : 'category.publish'; ?>')">
            <img src="images/<?= $row->published ? "tick.png" : "publish_x.png"; ?>" border="0" /></a></td>
          <td align="center"><?= $pageNav->orderUpIcon( $i, ($orderOk && !$orderHead), 'category.orderup' ) ?></td>
          <td align="center"><?= $pageNav->orderDownIcon( $i, $n, ($orderOk && !$orderTail), 'category.orderdown' ) ?></td>
          <td align="center" colspan="2"><input type="text" name="order[]" size="5" value="<?= $row->ordering ?>" class="text_area" style="text-align: center" /></td>
          <td align="center"><?= $row->id ?></td>
        </tr>
        <?php
        $k = 1 - $k;
      }
      ?>
      </table>
      <?php echo $pageNav->getListFooter(); ?>
      <input type="hidden" name="option" value="<?php echo $option; ?>" />
      <input type="hidden" name="task" value="<?= $priTask ?>" />
      <input type="hidden" name="boxchecked" value="0" />
      <input type="hidden" name="hidemainmenu" value="0" />
    </form>
    <?php
  }

  // ************************************************************************
  function edit( $row, $lists ) {
    global $my, $mainframe, $database, $option, $priTask, $subTask;
    global $WBG_CONFIG, $wbGalleryDB_cat;

    if( $row->file ){
      $imgPath = $WBG_CONFIG->path_thumb.$row->file;
    } else {
      $subCatImg = $wbGalleryDB_cat->getSubCatImg($row->id);
      if( $subCatImg->img_file )
        $imgPath = $WBG_CONFIG->path_thumb.$subCatImg->img_file;
    }
    if( $imgPath && file_exists($mainframe->getCfg('absolute_path').$imgPath) )
      $imgPath = $mainframe->getCfg('live_site').$imgPath;
    else
      $imgPath = $subCatImg = null;

    // Clean the Row for Form Use
    mosMakeHtmlSafe( $row, ENT_QUOTES, 'description' );

    ?>
    <script language="javascript" type="text/javascript">
    function submitbutton(pressbutton, section) {
      var form = document.adminForm;
      if (pressbutton == 'cancel') {
        submitform( pressbutton );
        return;
      }
      if ( form.name.value == "" ) {
        alert("Category must have a name");
      } else {
        cCatId();
        <?php getEditorContents( 'editor1', 'description' ) ; ?>
        submitform(pressbutton);
      }
    }
    function cCatId(){
      var form = document.adminForm;
      form.title.disabled = 0;
    }
    </script>
    <form action="index2.php" method="post" name="adminForm" enctype="multipart/form-data">
      <table class="adminheading">
        <tr>
          <th class="edit" valign="top">
            <font size="+1">wbGallery</font><br/>
            <?= $row->id ? 'Edit' : 'Add' ?> Collection Category<br/>
          </th>
        </tr>
      </table>
      <table class="adminform">
        <tr>
          <th colspan="3">Category Details</th>
        <tr>
        <tr>
          <td width="150">Category Name:</td>
          <td width="200"><input class="text_area" type="text" name="name" value="<?= stripslashes( $row->name ) ?>" size="50" maxlength="255" /></td>
          <?php if( $subCatImg->img_file ){ ?>
            <td rowspan="8" valign="top" align="left">
              <p>Sub Category Image Item</p>
              <img src="<?= $imgPath ?>" /></td>
          <?php } elseif( $imgPath ) { ?>
            <td rowspan="8" valign="top" align="left">
              <img src="<?= $imgPath ?>" />
              <p><input type="checkbox" name="_delFile" value="true" />
                Remove Category Defined Image</p></td>
          <?php } else { ?>
            <td rowspan="8" valign="top" align="left">
              <p>No Image Defined and No Sub Category Image Item</p></td>
          <?php } ?>
        </tr>
        <tr>
          <td>Category ID:</td>
          <td><input class="text_area" type="text" name="title" value="<?= stripslashes( $row->title ) ?>" size="50" maxlength="50" <?= strlen($row->title)?'disabled':'' ?>/></td>
        </tr>
        <tr><td colspan="2"><i>
          The category id is a reference element used for relationships within the system.<br/>
          Changing the id once defined may cause link errors for external sites. <a href="javascript:cCatId();">Click to Change</a>
          </i></td></tr>
        <tr valign="top">
          <td>Access Level:</td>
          <td><?php echo $lists['access']; ?></td>
        </tr>
        <tr>
          <td>Parent:</td>
          <td><?php echo $lists['parent_id']; ?></td>
        </tr>
        <tr>
          <td>Published:</td>
          <td><?php echo $lists['published']; ?></td>
        </tr>
        <tr>
          <td width="150">Category Image:</td>
          <td><input type="file" name="img" value="" /></td>
        </tr>
        <tr>
          <td colspan="2">Description:</td>
        </tr>
        <tr>
          <td colspan="3">
          <?php
            // parameters : areaname, content, hidden field, width, height, rows, cols
            editorArea( 'editor1',  $row->description , 'description', '100%;', '300', '60', '20' ) ;
          ?>
          </td>
        </tr>
      </table>
      <input type="hidden" name="ordering" value="<?= $row->ordering ?>" />
      <input type="hidden" name="file" value="<?= $row->file ?>" />
      <input type="hidden" name="option" value="<?= $option ?>" />
      <input type="hidden" name="task" value="<?= $priTask ?>" />
      <input type="hidden" name="id" value="<?= $row->id ?>" />
      <input type="hidden" name="hidemainmenu" value="0" />
    </form>
    <?php
  }

}