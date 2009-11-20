<?php
// $HeadURL: http://joomlacode.org/svn/joomgallery/JG-1.5/JG/trunk/administrator/components/com_joomgallery/changelog.php $
// $Id: changelog.php 449 2009-06-14 11:57:04Z aha $
/******************************************************************************\
**   JoomGallery  1.5                                                         **
**   By: JoomGallery::ProjectTeam                                             **
**   Copyright (C) 2008 - 2009  M. Andreas Boettcher                          **
**   Based on: JoomGallery 1.0.0 by JoomGallery::ProjectTeam                  **
**   Released under GNU GPL Public License                                    **
**   License: http://www.gnu.org/copyleft/gpl.html or have a look             **
**   at administrator/components/com_joomgallery/LICENSE.TXT                  **
\******************************************************************************/

defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.' );
?>

CHANGELOG JOOMGALLERY (since Version 1.5 BETA 1 -20081130-)

Legende / Legend:

* -> Security Fix
# -> Bug Fix
+ -> Addition
^ -> Change
- -> Removed
! -> Note

===============================================================================
                                   1.5.0.2
                                   20090614
===============================================================================
20090612
# Voting: faking results by sending false radio-values
  http://joomlacode.org/gf/project/joomgallery/tracker/?action=TrackerItemEdit&tracker_id=4518&tracker_item_id=16777

20090611
# no Joomfish translation of category title displayed in category view and pathway
  http://www.joomgallery.net/forum/index.php/topic,1681
  Danke @hermann
^ new display of subcategories in gallery view with dTree, made by Erftralle. Thanks!
# wrong calculation of catstartpage
  http://www.joomgallery.net/forum/index.php/topic,1747.0.html
  Thanks @hermann
^ new version of JAVA Applet: 4.3.3rc2 Rev 775

20090610
+ Thumbnail and detail image recreation in picture manager

20090608
# some fixes with wrong permissions

20090605
# no search for words including special characters

===============================================================================
                                    1.5.0.1  
                                   20090527
===============================================================================

20090526
+ added turkish and bosnian language
^ JAVA Upload, Upload with IE not possible, applet branch version
+ Pagination for subcategories can be now viewed in top and bottom

20090524
+ edit of category owner in backend
  http://www.forum.en.joomgallery.net/index.php?topic=803
  thank you @szopen

20090522
^ JAVA Upload: set picture quality to 1 to override the applet default of 0.8
  when uploading the original picture

20090521
^ new version of JAVA Applet: 4.3.2 Rev 735

20090519
# wrong call of Joom_ResizeImage
  http://www.forum.en.joomgallery.net/index.php?topic=1291

20090518
#^ wrong xml tag for install.sql without utf, added to uninstall xml section

20090512
+ display module title in position 'jg_detailbtm' if configured in module params
- dated $_REQUEST['is_editor'] check removed
^ on success the interface method 'createCategory' will return the ID of the 
  created category from now on

20090510
^ cursor right/left in opened slimbox results in a refreshed site with desired picture
  in detail view and closed slimbox view
  http://www.joomgallery.net/forum/index.php/topic,1610

20090507
^ wrong parameter in JFile::copy (batch upload) Danke Mambolus 
  http://www.joomgallery.net/forum/index.php/topic,1588

20090505
# category choice in picture manager still effective even when the category was
  deleted before -> set session variable to 0 when category deleted
  http://www.joomgallery.net/forum/index.php/topic,1581
# Use of Joom_Chmod instead of JPath::setPermissions
  because of problems on servers with wwwrun-problem

===============================================================================
                                    1.5.0
                                  20090503 
===============================================================================

20090502
# [Forum: http://www.forum.en.joomgallery.net/index.php?topic=1057.0]
  Do not call exec if it is disabled
^ new Codestyle for all frontend files

20090501
^ new version of JAVA Applet: 4.3.1 Rev 727

20090428
^ new version of JAVA Applet: 4.3.0 Rev 713
# it was possible to view unpublished categories
# detail view: redirect if incorrect image id

20090427
+ display subcategories in gallery view
+ new languages (italian, lithuanian)

20090422
+ column 'id' in picture manager
^ free setting of columns in all views

20090421
^ UNIX-LF for all frontend files
^ detail view compressed (Part 1)
# clearboth 
^ arrow.png from famfamfam
# CSS class .pngfile deleted from joom_settings.css
  because it already exists in joom.javascript.php (but moved upwards in this file)

20090420
# wrong thumbnail quality setting from configuration in creating detail pictures, 
  affects all upload methods, thank you to erftralle
  http://www.joomgallery.net/forum/index.php/topic,1535

20090419
# access to userpanel for registered user via known link if only admin access 
  allowed
# showing penultimating picture in slimbox offers not a 'next' link to last picture
# batch upload: uppercased extensions ignored

20090418
^ JoomFish: no translation in detail view
  http://www.forum.en.joomgallery.net/index.php?topic=852.msg3151#msg3151

20090414
+ new languages (persian, polish, finnish)
^ UNIX-LF for all admin files

20090412
# changes regarding xhtml validity
^ new version of JAVA Applet: 4.2.1c Rev 671
^ revised upload functions in backend and frontend:
    new checkbox 'debug' in backend, output of debug always when an error occurs
    new rollback function in case of error, deletion of already created files
    calculation of needed memory before resizing operations
    batch upload: working with zipped (sub)folders is now possible, 
                  extracting only relevant files
+ LF: new constants according to upload
             
20090408
# png fix (pngbehavior.htc) did not work with all search engine-friendly urls
  CSS class .pngfile moved to joom_settings.css
  change in pngbehavior.htc to get an absolute path to the blank.gif
# [Forum: http://www.joomgallery.net/forum/index.php/topic,1497.0.html]
  CSS floats were not cleared
# 9px*9px for arrow.png instead of 16px*16px

20090406
# Link "Back to Category Overview" if the user is in a sub-category

20090404
+ some plugin events added
# [Forum: http://www.forum.en.joomgallery.net/index.php?topic=1064]
  users could create more categories than set in the configuration

20090402
+ new method in interface for creating categories

20090317
^ new version of JAVA Applet: 4.2.1c Rev 658
^ JAVA Upload: check the php.ini setting 'session.cookie_httponly'
  if set and = 1 then build the parameter 'readCookieFrom Navigator=false'
  in Applet (new since V 4.2.1c) and provide the cookie with 
  sessionname=token in parameter 'specificHeaders'
# blank category name when Frontend settings->User Panel->Show Mini Thumbs->No
  in showusercats, thanks to 'erftralle'
  http://www.forum.en.joomgallery.net/index.php?topic=973

20090314
+ Cache time for update checker
+ optionally load modules only in certain views
^ Smileys are returned by the function Joom_GetSmileys() now

20090312
# Received http status 400 bad request' error
  http://sourceforge.net/forum/message.php?msg_id=6728044

20090307
^ only one CSS file for all views

20090306
# wrong path to 'loadAnimation.gif' from Thickbox3 with activated SEF
  http://www.forum.en.joomgallery.net/index.php?topic=928
^ Class Joom_DetailView added

20090303
# unnecessary copy procedure removed
  which caused an error on servers with wwwrun-problem
# workaround for servers with wwwrun-problem
  make temp path writeable if it is not

20090301
^ function displayThumb() in Interface modified to consider parameter 'piclink' 
  in creating links to category or detail view, needed for JoomImages
  including of modules.class.php should be now deprecated with the module 1.5.1

20090228
^ new version of JAVA Applet: 4.2.0 Rev 642

20090227
+ possibility of auto update with cURL added
^ issue in motiongallery.js in combination with other JS which make a premature
  call of onresize() 

20090226
- Output of version in frontend removed
# [Forum: http://www.forum.en.joomgallery.net/index.php?topic=863]
  Output of asterisk comments in java upload page fixed

20090225
# Workaround in Joom_Ambit class for PHP4

20090224
^ Migration manager is now ready for different migration scripts
# On servers with wwwrun problem images could not be deleted
- Some unnecessasy wrapper functions removed

===============================================================================
                                     RC2
                                  20090222 
===============================================================================

20090222
! Bridge: deprecated As of version 1.5 RC2

20090221
^ new version of JAVA Applet: 4.1.0 Rev 526
# 'new' image in category view not visible with activated SEF
# accordion not working with activated SEF

20090218
# users could rate their own images
# several fixes with numbering the files and titles in FTP and Batchupload 

20090217
* Comments settings checked after form submission

20090215
# settings for the limit and the search text in category manager were also
  used in picture manager and vice versa
+ some module positions added
+ delete copy of the feed module on position 'joom_cpanel' when joomgallery
  gets uninstalled
+ update checker displays a notice on more pages now
+ 'Your system is up-to-date.' is displayed in admin menu
# bugs in reordering pictures in the picture manager
  

20090214
# Slideshow does not work with Opera and Safari with activated transition effects
  in JoomGallery backend.
# Comments-IP has been visible for reg. Users, Link to whois didn't work, 
  reg. Users were able to delete every comment
# Slimbox: opening the last picture in slimbox and click on 'previous' shows
  the same picture again: http://www.joomgallery.net/forum/index.php/topic,1190

20090213
# Moving (detail)pictures failed
# Deleting comments in frontend

20090212
# Deleting categories in frontend

20090211
# Settings for anonymous comments not saveable

===============================================================================
                                     RC
                                  20090208
===============================================================================
20090208
# "Last Commented" view

20090207
+ Functions returning comments in interface
# getPictureLink in interface


20090204
# Bug in countstop function because of a database column with primary key

20090203
# increasing the hit counter of the neighboured pictures even when not clicked
  preloading of this pictures in Slimbox deactivated
# no increasing of the hit counter when originals are shown in Slim/Thickbox

20090202
^ image counter increase now in one query

20090130
+ optional category filtering in Interface
^ do not read RSS file if update check is disabled

20090128
+ Option for update check in the configuration manager
# make category titles in tooltips JS safe

20090127
^ new face for admin menu (CPanel)
+ automatic update check for all JoomGallery extensions and JoomGallery itself

20090120
+ Possibility to assign owner of picture in new picture form (Backend)
+ Possibility to change owner of picture in picture edit form (Backend)

20090119
# Workaround for servers with wwwrun problem
- 'tn_' backwards compatibility removed

20090117
^ Configuration manager revised, new functions introduced to improve clarity

20090111
# Migration creates wrong catpaths if iteration depth > 2, 
  instead test1_1/test2_2/test3_3 it generates test1_1test2_2/test3_3

20090109
- Lighbox
+ Files for Editorbutton
^ All javascript codes are added to the head now
# wrong catpath for thumbnails of parent categories was used
  in joom.viewcategory.html.php
# wrong default value for id in #__joomgallery_config
# Cooliris does not show all pictures on paginated categories
^ new JAVA applet version V 4.0.0
# Thickbox 3, keys for next and previous interchanged
^ little hack to show a category thumb in gallery view without showing them in 
  category view, only working with 'own choice' and approved/not published

20090103
# if clicking on cpanel in configuration manager the function joom_testDefaultValues()
  is invoked without a need, reactivated function submitbutton

20090101
# Slimbox: dynamically ignore of doublets
# Thickbox3: dynamically ignore of doublets
# accordion.js gives a javascript error with function 'load' -> 'domready'

20081230
^ Configuration is saved in the database now
  -> new table #__joomgallery_config
^ number of globals reduced
  -> usage of contants like JPATH_ROOT, JPATH_COMPONENT, _JOOM_LIVE_SITE

20081229
+ Navigation in User panel

20081228
^ loading of mootools.js with the help of JHTML
  in order to prevent an unnecessary integration
^ JFile, JFolder, JPath

20081227
^ ini language files for exif and iptc data introduced and old language files removed
+ install.sql and uninstall.sql introduced
^ unnecessary hidden fields removed/replaced by url parameters

20081225
# send2friend: no value in hidden input field 'Itemid'
# cooliris: with no original pictures the thumb and a failure symbol will be 
  shown in cooliris -> show the detail picture

20081224
# Batch Upload: when the zip upload fails, the temp folder defined in
  configuration manager will be deleted. At second failed try the Joomla! folder 
  'media' will be moved

20081223
^ ini language files introduced and old language files removed
^ small performance upgrades in Joom_GetCatPath() and Joom_PagingCategory()
# User panel: the full path of the categories not shown in upload and creation
  of user categories
  http://www.joomgallery.net/forum/index.php/topic,914

20081222
+ Additional parameter in getPageHeader() of interface class
  (workaround for $mainframe->addCustomHeadTag)
^ all 'create table' changed from TYPE=MyISAM to ENGINE=MyISAM, 
  TYPE deprecated and removed since mySQL Version 5.1

20081221
# [14080] http://joomlacode.org/gf/project/joomgallery/tracker/?action=TrackerItemEdit&tracker_item_id=14080
  1) with activated page navigation in category view the user sorting does not work
     in subpages > 1
  2) with activated page navigation a change in user sorting on a subpage > 1 opens
     subpage 1, not the actual
# Slimbox does not work in detail view if backend setting 
  'Frontend settings->Downsize pictures by Javascript:' set to 'No'

20081220
# Interface in Joomla 1.5 fixed
# URL in link to user profiles

20081212
# cb/cbe linking in module (modules.class.php) deactivated until the
       functions from JG V 1.0 are fully migrated and working
# comments with linebreaks (\r\n or \n) are shown in detail view with 'rn'
       changed before saving in db with nl2br and striptags
# Notice in JAVA upload: undefined $bugtest
# several fixes and changes to let the module joomimages work

20081210
# [Forum: http://www.forum.en.joomgallery.net/index.php?topic=487]
  the function updateOrder() was not yet replaced by the J! 1.5 function reorder()
