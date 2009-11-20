<?php
// $HeadURL: http://joomlacode.org/svn/joomgallery/JG-1.5/JG/trunk/components/com_joomgallery/includes/joom.slideshow.php $
// $Id: joom.slideshow.php 449 2009-06-14 11:57:04Z aha $
/******************************************************************************\
**   JoomGallery  1.5                                                         **
**   By: JoomGallery::ProjectTeam                                             **
**   Copyright (C) 2008 - 2009  M. Andreas Boettcher                          **
**   Based on: JoomGallery 1.0.0 by JoomGallery::ProjectTeam                  **
**   Released under GNU GPL Public License                                    **
**   License: http://www.gnu.org/copyleft/gpl.html or have a look             **
**   at administrator/components/com_joomgallery/LICENSE.TXT                  **
\******************************************************************************/

# Don't allow direct linking
defined( '_JEXEC' ) or die( 'Direct Access to this location is not allowed.');
?>
<script language = "Javascript" type = "text/javascript">
<!--

photo = new joom_createphotoobject()

function joom_createphotoobject() {
  //Get config
  

  this.minis        = <?php echo $config->jg_minis; ?>;

  this.time         = <?php echo $config->jg_slideshow_timer; ?>*1000;
  this.usefilter    = <?php echo $config->jg_slideshow_usefilter; ?>;
  this.filterchance = <?php echo $config->jg_slideshow_filterbychance; ?>;
  this.filtertimer  = <?php echo $config->jg_slideshow_filtertimer; ?>;
  this.resize       = <?php echo $config->jg_resizetomaxwidth; ?>;
  this.maxwidth     = <?php echo $config->jg_maxwidth; ?>;

  //Get language
  this.pause_data = "<img src=\"<?php echo _JOOM_LIVE_SITE."components/com_joomgallery/assets/images/control_pause.png";?>\" alt=\"<?php echo JText::_('JGS_PAUSE'); ?>\" class=\"pngfile jg_icon\" />";
  this.goon_data  = "<img src=\"<?php echo _JOOM_LIVE_SITE."components/com_joomgallery/assets/images/control_play.png";?>\" alt=\"<?php echo JText::_('JGS_GO_ON'); ?>\" class=\"pngfile jg_icon\" />";
  this.repeat     = "<?php echo JText::_('JGS_ALERT_ONCE_AGAIN',true); ?>";

  //define Variables
  this.img           = "jg_photo_big";
  this.filter        = false;
  this.repeater      = 1;
  this.effekt        = new Array();

  //get Details to change
  this.tochange = new Array();
<?php
  if($config->jg_showdetail!=0) {
?>
    this.tochange[0] = new Array(<?php echo ( $config->jg_showdetail )            ? "1" : "0"; ?>,'jg_photo_title');
    this.tochange[1] = new Array(<?php echo ( $config->jg_showdetaildescription ) ? "1" : "0"; ?>,'jg_photo_description');
    this.tochange[2] = new Array(<?php echo ( $config->jg_showdetaildatum )       ? "1" : "0"; ?>,'jg_photo_date');
    this.tochange[3] = new Array(<?php echo ( $config->jg_showdetailhits )        ? "1" : "0"; ?>,'jg_photo_hits');
    this.tochange[4] = new Array(<?php echo ( $config->jg_showdetailrating )      ? "1" : "0"; ?>,'jg_photo_rating');
    this.tochange[5] = new Array(<?php echo ( $config->jg_showdetailfilesize )    ? "1" : "0"; ?>,'jg_photo_filesize');
    this.tochange[6] = new Array(<?php echo ( $config->jg_showdetailauthor )      ? "1" : "0"; ?>,'jg_photo_author');
<?php
  }
?>

  this.id           = new Array();
  this.source       = new Array();
  this.title        = new Array();
  this.description  = new Array();
  this.date         = new Array();
  this.hits         = new Array();
  this.rating       = new Array();
  this.filesize     = new Array();
  this.author       = new Array();
  this.detail       = new Array(this.title, this.description, this.date, this.hits, this.rating, this.filesize, this.author);

  this.start        = joom_photo_start;
  this.info         = joom_photo_info;
  this.next         = joom_photo_next;
  this.stop         = joom_photo_stop;
  this.pause        = joom_photo_pause;
  this.goon         = joom_photo_goon;
  this.repeatstatus = joom_photo_repeatstatus;

  this.change       = joom_photo_change;
  this.preloadimg   = new Array();
  this.preload      = joom_photo_preload;
  this.loadstatus   = joom_photo_loadstatus;
}


function joom_photo_start() {
  this.startimg = document.jg_slideshow_form.jg_number.value;
  this.counter = 0;
  while(this.id[this.counter]!=this.startimg) {
    this.counter++;
  }
  document.getElementById('jg_goon').innerHTML = "";
  this.preload();
  this.changetimeout =  window.setTimeout('photo.next()',this.time/2);
}


function joom_photo_stop() {
  location.href = location.href.replace('#joomimg','');
}


function joom_photo_goon() {
  document.getElementById('jg_goon').style.visibility = "hidden";
  document.getElementById('jg_goon').innerHTML = "";
  document.getElementById('jg_pause').style.visibility = "visible";
  document.getElementById('jg_pause').innerHTML = this.pause_data;
  this.changetimeout = window.setTimeout('photo.next()',500);
}


function joom_photo_pause() {
  document.getElementById('jg_pause').style.visibility = "hidden";
  document.getElementById('jg_pause').innerHTML = "";
  document.getElementById('jg_goon').style.visibility = "visible";
  document.getElementById('jg_goon').innerHTML = this.goon_data;
  clearTimeout(this.changetimeout);
}


function joom_photo_repeatstatus() {
  (this.repeater!=0) ? this.repeater = 0 : this.repeater = 1;
  if (this.repeater == 0){
    document.getElementById('jg_repeat').innerHTML = "";
  }
}


function joom_photo_info(id, source, title, description, date, hits, rating, filesize, author) {
  this.id[this.id.length] = id;
  this.source[this.source.length] = source;
  this.title[this.title.length] = title;
  if (description == '') {
    description='&nbsp;';
  }
  this.description[this.description.length] = description;
  this.date[this.date.length] = date;
  this.hits[this.hits.length] = hits;
  this.rating[this.rating.length] = rating;
  this.filesize[this.filesize.length] = filesize;
  if (author == '') {
    author='&nbsp;';
  }
  this.author[this.author.length] = author;
}


function joom_photo_next() {
  if(this.loadstatus()) {
    this.counter++;
    if(this.counter >= this.source.length) {
      this.counter = 0;
      this.mini1 = "jg_mini_"+this.id[this.source.length-1];
    } else {
      this.mini1 = "jg_mini_"+this.id[this.counter-1];
    }
    this.mini2 = "jg_mini_"+this.id[(this.counter)];
    this.repeatonce = true;
    if(this.id[this.counter]==this.startimg && this.repeater) {
      this.repeatonce = confirm(this.repeat);
    }
    if(this.repeatonce) {
      this.miniimg = document.getElementById(this.img);

      //activate transition efects only in IE
      var browserName=navigator.appName;
      if(browserName=="Microsoft Internet Explorer" &&
        this.filter==true && this.usefilter == 1) {
        if(this.filterchance != 0) {
          for(i=0;i<23;i++) {
            this.effekt[i] = i;
          }
          this.obchance = this.effekt[parseInt((Math.random() * 100) % 23)];
          this.miniimg.style.filter = "revealTrans(Duration="+this.filtertimer+",Transition="+this.obchance+")";
          this.miniimg.filters.revealTrans.Apply();
        } else {
          this.miniimg.style.filter = "blendTrans(Duration="+this.filtertimer+")";
          this.miniimg.filters.blendTrans.Apply();
        }
      }
      this.nextimage = new Image();
      this.nextimage.src = this.source[this.counter];
      if(this.resize) {
       this.ratio = Math.max(this.nextimage.width,this.nextimage.height);
       this.ratio = this.ratio/this.maxwidth;
       this.ratio = Math.max(this.ratio, 1.0);
       this.width = Math.ceil(this.nextimage.width / this.ratio);
       this.height = Math.ceil(this.nextimage.height / this.ratio);
     } else {
       this.width=this.nextimage.width;
       this.height=this.nextimage.height;
     }
      this.miniimg.src=this.nextimage.src;
      this.miniimg.width = this.width;
      this.miniimg.height = this.height;
      this.miniimg.style.width = this.width+"px";
      this.miniimg.style.height = this.height+"px";

      //activate transition efects only in IE
      if(browserName=="Microsoft Internet Explorer" && this.filter != 0 && this.usefilter != 0) {
        (this.filterchance != 0) ? this.miniimg.filters.revealTrans.Play() : this.miniimg.filters.blendTrans.Play();
      }
      /* if activated error in IE7
      if(browserName!="Microsoft Internet Explorer" && this.minis!=0) {
        document.getElementById(this.mini1).style.borderColor = this.color_normal;
        document.getElementById(this.mini2).style.borderColor = this.color_active;
      }
      */
      this.change();
      this.preload();
      this.changetimeout = window.setTimeout('photo.next()',this.time);
    } else {
      this.stop();
    }
  } else {
    window.setTimeout('photo.next()',100);
  }
}


function joom_photo_change() {
  for(i=0;i<this.tochange.length;i++) {
    if(this.tochange[i][0]!=0 && document.getElementById(this.tochange[i][1])) {
      document.getElementById(this.tochange[i][1]).innerHTML = this.detail[i][this.counter];
    }
  }
}


function joom_photo_preload() {
  if(this.counter+1 >= this.source.length) {
    this.preloadcounter = 0;
  } else {
    this.preloadcounter = this.counter+1;
  }
  this.preloadimg[this.preloadcounter] = new Image();
  this.preloadimg[this.preloadcounter].src = this.source[this.preloadcounter];
}


function joom_photo_loadstatus() {
  if(this.preloadimg[this.preloadcounter].complete) {
    return true;
  } else {
    return false;
  }
}
<?php

$jg_slideshow_data = '';
$jg_between = "\",\"";
for($i = 0; $i < sizeof($fileinfo['id']); $i++)
{
  $jg_slideshow_data .= "photo.info(\"";
  $jg_slideshow_data .= $fileinfo['id'][$i];
  $jg_slideshow_data .= $jg_between;
  $jg_slideshow_data .= $fileinfo['source'][$i];
  $jg_slideshow_data .= $jg_between;
  $jg_slideshow_data .= Joom_FixForJS($fileinfo['title'][$i]);
  $jg_slideshow_data .= $jg_between;
  $jg_slideshow_data .= Joom_FixForJS($fileinfo['description'][$i]);
  $jg_slideshow_data .= $jg_between;
  if($fileinfo['date'][$i] != '')
  {
    $jg_slideshow_data .= strftime($config->jg_dateformat,$fileinfo['date'][$i]);
  }
  else
  {
    $jg_slideshow_data .= '';
  }

  $jg_slideshow_data .= $jg_between;
  $jg_slideshow_data .= $fileinfo['hits'][$i];
  $jg_slideshow_data .= $jg_between;
  if($fileinfo['rating1'][$i] > 0)
  {
    $jg_slideshow_data .= number_format( $fileinfo['rating2'][$i] / $fileinfo['rating1'][$i], 2, ",", "." )."(".$fileinfo['rating1'][$i].JText::_('JGS_VOTES').")";
  }
  else
  {
    $jg_slideshow_data .= JText::_('JGS_NO_VOTES');
  }
  $jg_slideshow_data .= $jg_between;
  $jg_slideshow_data .= number_format($fileinfo['filesize'][$i]/1024,2,",", "." )." KB";
  $jg_slideshow_data .= $jg_between;
  $jg_slideshow_data .= Joom_FixForJS($fileinfo['author'][$i]);
  $jg_slideshow_data .= "\");\n";
}
echo $jg_slideshow_data;
?>
//-->
</script>
