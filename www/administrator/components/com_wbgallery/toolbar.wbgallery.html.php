<?php

// ************************************************************************
//
// wbGallery
// Licensed under the GNU/GPL Open Source License
// (c) 2008 Webuddha.com, The Holodyn Corporation
//
// Joomla Toolbar Output
//
// ************************************************************************

defined( '_VALID_MOS' ) or die( 'Restricted access' );

// ************************************************************************
class TOOLBAR_wbgallery {

  // ************************************************************************
  function _LIST_IMAGE() {
    global $id, $cid, $priTask, $subTask;
    mosMenuBar::startTable();
    mosMenuBar::custom('home', 'back.png', 'back_f2.png', 'Home', false);
    mosMenuBar::spacer();
    mosMenuBar::custom($priTask.'.upload', 'upload.png', 'upload_f2.png', 'Upload', false);
    mosMenuBar::spacer();
    mosMenuBar::editListX($priTask.'.edit');
    mosMenuBar::spacer();
    mosMenuBar::deleteListX('',$priTask.'.remove');
    mosMenuBar::spacer();
    mosMenuBar::publishList($priTask.'.publish');
    mosMenuBar::spacer();
    mosMenuBar::unpublishList($priTask.'.unpublish');
    mosMenuBar::endTable();
  }

  // ************************************************************************
  function _LIST() {
    global $id, $cid, $priTask, $subTask;
    mosMenuBar::startTable();
    mosMenuBar::custom('home', 'back.png', 'back_f2.png', 'Home', false);
    mosMenuBar::spacer();
    mosMenuBar::addNewX($priTask.'.new');
    mosMenuBar::spacer();
    mosMenuBar::editListX($priTask.'.edit');
    mosMenuBar::spacer();
    mosMenuBar::deleteListX('',$priTask.'.remove');
    mosMenuBar::spacer();
    mosMenuBar::publishList($priTask.'.publish');
    mosMenuBar::spacer();
    mosMenuBar::unpublishList($priTask.'.unpublish');
    mosMenuBar::endTable();
  }

  // ************************************************************************
  function _EDIT() {
    global $id, $cid, $priTask, $subTask;
    mosMenuBar::startTable();
    mosMenuBar::spacer();
    mosMenuBar::save($priTask.'.save');
    mosMenuBar::spacer();
    mosMenuBar::apply($priTask.'.apply');
    mosMenuBar::spacer();
    mosMenuBar::cancel($priTask.'.cancel', 'Close');
    mosMenuBar::endTable();
  }

  // ************************************************************************
  function _UPLOAD() {
    global $id, $cid, $priTask, $subTask;
    mosMenuBar::startTable();
    mosMenuBar::custom('home', 'back.png', 'back_f2.png', 'Home', false);
    mosMenuBar::spacer();
    mosMenuBar::custom($priTask.'.upload_save', 'upload.png', 'upload_f2.png', 'Upload', false);
    mosMenuBar::spacer();
    mosMenuBar::custom($priTask.'', 'forward.png', 'forward_f2.png', 'View', false);
    mosMenuBar::spacer();
    mosMenuBar::cancel($priTask.'.cancel', 'Cancel');
    mosMenuBar::endTable();
  }

}
