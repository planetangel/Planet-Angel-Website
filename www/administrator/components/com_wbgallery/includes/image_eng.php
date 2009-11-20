<?php

// ************************************************************************
//
// wbGallery
// Licensed under the GNU/GPL Open Source License
// (c) 2008 Webuddha.com, The Holodyn Corporation
//
// Image Processing
//
// ************************************************************************

defined( '_VALID_MOS' ) or die( 'Restricted access' );

// ************************************************************************
// Setup Error Reporting
// error_reporting(E_ALL & E_STRICT);
// Set Run Time in Seconds
set_time_limit(180);

class wbGallery_img_eng {

  // ************************************************************************
  function add($filePath, $fileName, $fileType, &$defRow, $store=true ){
    global $my, $mainframe, $database, $option, $priTask, $subTask;
    global $WBG_CONFIG, $wbGalleryDB_cat;

    $time = time().rand(0,99999);
    $date = date('Y_m');
    $fileName = preg_replace('/[\s|\\\|\"|\&|\?|\']+/','_',$fileName);
    $fileName = preg_replace('/\_+/','_',$fileName);
    $fileExt  = preg_replace('/^\w+\//','',$fileType);
    $newFileName = $date.'/'.$time.'.'.$fileExt;

    // Initial File Should be the Largest
    $origInfo = getimagesize($filePath);


    // Debug
    echo "Adding File: ".$fileName.' -> '.$newFileName.'<br/>';

    for( $iType = 1; $iType <= 5; $iType++ ){

      $active = 0;
      // Process from Largest to Smallest
      switch( $iType ){
        case 1:
          // ORIGINAL
          $active   = $WBG_CONFIG->save_original;
          $width    = 0;
          $height   = 0;
          $quality  = 0;
          $path     = $mainframe->getCfg('absolute_path').$WBG_CONFIG->path_original;
          break;
        case 2:
          // LARGE
          $active   = $WBG_CONFIG->save_large;
          $width    = $WBG_CONFIG->width_large;
          $height   = $WBG_CONFIG->height_large;
          $quality  = $WBG_CONFIG->quality_large;
          $path     = $mainframe->getCfg('absolute_path').$WBG_CONFIG->path_large;
          break;
        case 3:
          // MEDIUM
          $active   = $WBG_CONFIG->save_medium;
          $width    = $WBG_CONFIG->width_medium;
          $height   = $WBG_CONFIG->height_medium;
          $quality  = $WBG_CONFIG->quality_medium;
          $path     = $mainframe->getCfg('absolute_path').$WBG_CONFIG->path_medium;
          break;
        case 4:
          // THUMB
          $active   = $WBG_CONFIG->save_thumb;
          $width    = $WBG_CONFIG->width_thumb;
          $height   = $WBG_CONFIG->height_thumb;
          $quality  = $WBG_CONFIG->quality_thumb;
          $path     = $mainframe->getCfg('absolute_path').$WBG_CONFIG->path_thumb;
          break;
        case 5:
          // TACK
          $active   = $WBG_CONFIG->save_tack;
          $width    = $WBG_CONFIG->width_tack;
          $height   = $WBG_CONFIG->height_tack;
          $quality  = $WBG_CONFIG->quality_tack;
          $path     = $mainframe->getCfg('absolute_path').$WBG_CONFIG->path_tack;
          break;
      }

      // Process the Image for Step
      // Check / Create Destination
      // Copy Image
      // Resize Copied
      if( $active ){
        $fullPath = $path.$newFileName;
        if(!is_writable($path)){
          echo "<script> alert('Permission Denied for $path'); window.history.go(-1); </script>\n";
          exit;
        }
        if(!file_exists($path.$date)){
          if(!mkdir($path.$date)){
            echo "<script> alert('Failed to Create Category Folder'); window.history.go(-1); </script>\n";
            exit;
          }
          mosChmod($path.$date, 0777);
        }
        if(!copy($filePath,$fullPath)){
          echo "<script> alert('Failed to Save Image'); window.history.go(-1); </script>\n";
          exit;
        }
        if( $width && $height )
          if( !$this->resize($fullPath, $fileType, $width, $height) ){
            echo "<script> alert('Error Resizing Image $fileName'); window.history.go(-1); </script>\n";
            exit;
          } else
            $imgInfo = getimagesize($fullPath);

      }

    }

    // Debug
    echo "Creating Database Record: ".$fileName.'<br/>';

    // Store Record
    $row = new wbGalleryDB_img($database);
    $row->file          = $newFileName;
    $row->cat_id        = $defRow->cat_id;
    $row->name          = (strlen($defRow->name) ? $defRow->name : preg_replace('/\.\w+$/','',$fileName));
    $row->description   = $defRow->description;
    $row->photographer  = $defRow->photographer;
    $row->price         = $defRow->price;
    $row->sku           = $defRow->sku;
    $row->publised      = $defRow->publised;
    $row->created       = date('Y-m-d H:i:s');
    $row->modified      = $row->created;
    $row->ordering      = 0;
    if( is_array($origInfo) ){
      $row->width         = $origInfo[0];
      $row->height        = $origInfo[1];
      $row->size          = $origInfo['bits'];
    } elseif( is_array($imgInfo) ){
      $row->width         = $imgInfo[0];
      $row->height        = $imgInfo[1];
      $row->size          = $imgInfo['bits'];
    }
    if( $store ){
      // Check
      if (!$row->check()) {
        echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
        exit();
      }
      // Store
      if (!$row->store()) {
        echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
        exit();
      }
      // Update Ordering
      $row->updateOrder('cat_id = '.(int)$row->cat_id);
      echo "Image Stored Successfully <br/><br/>";
      return true;
    } else
      return $row;

  }

  // ************************************************************************
  function resize($file, $type, $width, $height){
    global $my, $mainframe, $database, $option, $priTask, $subTask;
    global $WBG_CONFIG, $wbGalleryDB_cat;

    // Debug
    echo "Resizing: ".$file.' to '.$width.'x'.$height.'<br/>';

    // Specify the Minimum Image Size for ImageMagick Processing
    $im_min_limit = 0;

    // Collect the Image Information
    $imgInfo = getimagesize($file);
    printf('Loading image %s, size %s * %s, bpp %s... <br/>',$file, $imgInfo[0], $imgInfo[1], $imgInfo['bits']);

    if( function_exists('image_type_to_mime_type') && function_exists('exif_imagetype') )
      echo "File Format Confirmed: ".image_type_to_mime_type(exif_imagetype( $file ))." - $type<br/>";

    // Handle Memory Management
    if( $WBG_CONFIG->use_memManager ){
      $memoryLimit = ini_get('memory_limit');
      echo 'Memory Limit: '.$memoryLimit." <br/>";
      $memoryUsage = $this->mem_get_usage();
      if( $memoryUsage ){
        echo "Memory Usage is $memoryUsage <br/>";
        $memoryNeeded = round(($imgInfo[0] * $imgInfo[1] * $imgInfo['bits'] * $imgInfo['channels'] / 8 + Pow(2, 16)) * 1.65)*2;
        echo "Memory Required is $memoryNeeded <br/>";
        if($memoryUsage + $memoryNeeded > (integer)ini_get('memory_limit') * pow(1024, 2)) {
          $memLimit = (integer)ini_get('memory_limit') + ceil((($memoryUsage + $memoryNeeded) - (integer) ini_get('memory_limit') * pow(1024, 2)) / pow(1024, 2)) . 'M';
          echo "Set Memory Limit to $memLimit <br/>";
          ini_set('memory_limit', $memLimit);
        }
      } else {
        echo "Memory Usage Information Unavailable... <br>";
      }
    }

    // Use Mogrify (ImageMagick) if JPEG and Available
    if( $WBG_CONFIG->use_ImageMagik && $WBG_CONFIG->path_ImageMagik && $WBG_CONFIG->file_ImageMagik ){
      if( in_array($type,Array('image/jpeg','image/jpg','image/pjpeg')) ){
        // Debug
        echo "Using Image Magik for JPEG Processing... <br/>";
        if( $imgInfo[0] > $im_min_limit || $imgInfo[1] > $im_min_limit ){
          exec('ls '.$WBG_CONFIG->path_ImageMagik.' | grep '.$WBG_CONFIG->file_ImageMagik,$res);
          $res = join(' ',$res);
          if(preg_match('/'.$WBG_CONFIG->file_ImageMagik.'/',$res)){
            echo 'Grep Found '.$WBG_CONFIG->file_ImageMagik.' in command list<br/>';
            echo 'Attempting to Process with ImageMagic<br/>';
            $imCommand = $WBG_CONFIG->path_ImageMagik.$WBG_CONFIG->file_ImageMagik.' -resize '.$width.'x'.$height.' '.$file;
            echo 'Exec: '.$imCommand.'<br/>';
            exec($imCommand,$res,$code);
            if( $code == 0 )
              return true;
            else
              echo "Image Magick Failed with Error Code $code... Using GD Library <br/>".join(' ',$res)."<br/>";
          } else
            echo "Image Magik Not Found [ $res ]... Using GD Library <br/>";
        } else
          echo "Below Image Magik Minium... Using GD Library <br/>";
      } // JPEG Mogrify
      else
        echo "Using PHP Image Functions <br/>";
    }
    else
      echo "Using PHP Image Functions <br/>";

    // Create Memory Map
    $oldImage = null;
    switch($type){
      case 'image/jpeg':
      case 'image/jpg':
      case 'image/pjpeg':
        echo "JPEG Detected <br/>";
        if( $this->CanonPowershotS70($file) )
          die("This image was made on a Canon Powershot S70");
        if( !$this->checkValidJPEG($file) )
          die("This is Not a Valid JPEG");
        $oldImage = @imagecreatefromjpeg($file);
        break;
      case 'image/png':
        echo "PNG Detected <br/>";
        $oldImage = @imagecreatefrompng($file);
        break;
      case 'image/gif':
        echo "GIF Detected <br/>";
        $oldImage = @imagecreatefromgif($file);
        break;
    }
    if(!$oldImage){
      echo "Failed to create Image Map <br/>";
      return false;
    }

    // Map Created
    echo "Created Image Map - Ready to Convert <br/>";
    $perc_w = $width / $imgInfo[0];
    $perc_h = $height / $imgInfo[1];
    if($perc_h > $perc_w){
      $height = round($imgInfo[1] * $perc_w);
    } else {
      $width = round($imgInfo[0] * $perc_h);
    }

    // Debug
    echo 'Converting to '.$width.'x'.$height.'<br/>';
    $newImage = imagecreatetruecolor($width, $height);
    imagecopyresampled($newImage, $oldImage, 0, 0, 0, 0, $width, $height, $imgInfo[0], $imgInfo[1]);

    switch ($type){
      case 'image/jpeg':
      case 'image/jpg':
      case 'image/pjpeg':
        imagejpeg($newImage, $file);
        break;
      case 'image/png':
        imagepng($newImage, $file);
        break;
      case 'image/gif':
        imagegif($newImage, $file);
        break;
    }

    echo "Resizing Complete <br/>";
    imagedestroy($newImage);
    imagedestroy($oldImage);

    return true;
  }

  // ************************************************************************
  // Canon Powershot Check
  function CanonPowershotS70($filename) {
    $thereturn = false;
    $handle = fopen($filename, "r");
    $contents = fread($handle, 159);
    echo "Checking for Canon Powershot S70: ";
    fclose($handle);
    if (substr($contents, 156, 3) == "S70") {
      echo "Header Found <br/>";
      $thereturn = true;
    }
    echo "Header NOT Found <br/>";
    return $thereturn;
  }

  // ************************************************************************
  // Check JPEG Proper Header Formatting
  function checkValidJPEG($f, $fix=false ){
    # [070203]
    # check for jpeg file header and footer - also try to fix it
    echo "Checking if Valid JPEG Header Exists <br/>";
    if ( false !== (@$fd = fopen($f, 'r+b' )) ){
      if ( fread($fd,2)==chr(255).chr(216) ){
        fseek( $fd, -2, SEEK_END );
        if ( fread($fd,2)==chr(255).chr(217) ){
          fclose($fd);
          echo "Valid JPEG Header Found <br/>";
          return true;
        }else{
          if ( $fix && fwrite($fd,chr(255).chr(217)) ){return true;}
          fclose($fd);
          echo "Invalid Header <br/>";
          return false;
        }
      } else {
        fclose($fd);
        echo "Could Not Read Header <br/>";
        return false;
      }
    } else {
      echo "Could Not Open Image File <br/>";
      return false;
    }
  }

  // ************************************************************************
  // Memory Limit Function
  function mem_get_usage(){
    if( function_exists('memory_get_usage') )
      return memory_get_usage();
    // If its Windows
    // Tested on Win XP Pro SP2. Should work on Win 2003 Server too
    // Doesn't work for 2000
    // If you need it to work for 2000 look at http://us2.php.net/manual/en/function.memory-get-usage.php#54642
    if ( substr(PHP_OS,0,3) == 'WIN'){
      echo "memory_get_usage: Win32 Detected <br/>";
      if ( substr( PHP_OS, 0, 3 ) == 'WIN' ){
        $output = array();
        exec( 'tasklist /FI "PID eq ' . getmypid() . '" /FO LIST', $output );
        return preg_replace( '/[\D]/', '', $output[5] ) * 1024;
      }
    } else {
      echo "memory_get_usage: UNIX Detected, Check processes <br/>";
      //We now assume the OS is UNIX
      //Tested on Mac OS X 10.4.6 and Linux Red Hat Enterprise 4
      //This should work on most UNIX systems
      $pid = getmypid();
      exec("ps -eo%mem,rss,pid | grep $pid", $output);
      $output = explode("  ", $output[0]);
      //rss is given in 1024 byte units
      return $output[1] * 1024;
    }
    return null;
  }

  // ************************************************************************
  // Remove Image Files
  function remove( $imgFile ){
    global $mainframe, $WBG_CONFIG;;
    if( $imgFile ){
      for( $iType = 1; $iType <= 5; $iType++ ){
        switch( $iType ){
          case 1:
            // ORIGINAL
            $path     = $mainframe->getCfg('absolute_path').$WBG_CONFIG->path_original;
            break;
          case 2:
            // LARGE
            $path     = $mainframe->getCfg('absolute_path').$WBG_CONFIG->path_large;
            break;
          case 3:
            // MEDIUM
            $path     = $mainframe->getCfg('absolute_path').$WBG_CONFIG->path_medium;
            break;
          case 4:
            // THUMB
            $path     = $mainframe->getCfg('absolute_path').$WBG_CONFIG->path_thumb;
            break;
          case 5:
            // TACK
            $path     = $mainframe->getCfg('absolute_path').$WBG_CONFIG->path_tack;
            break;
        }
        if(file_exists($path.$imgFile)){
          if(!unlink($path.$imgFile)){
            echo "<script> alert('Failed to remove image: ".$path.$imgFile."'); window.history.go(-1); </script>\n";
            return false;
          }
        }
      }
      return true;
    }
    return false;
  }

}
