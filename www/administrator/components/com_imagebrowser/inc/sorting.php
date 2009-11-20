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

// Sort array of objects by attribute
class t_object_sorter {
   var $object_array;
   var $sort_by;
  
   function _comp($a,$b) {
       $key = $this->sort_by;
       if ($this->object_array[$a]->$key == $this->object_array[$b]->$key) return 0;
       return ($this->object_array[$a]->$key < $this->object_array[$b]->$key) ? -1 : 1;
   }
  
   function sort(&$object_array, $sort_by) {
       $this->object_array = $object_array;
       $this->sort_by = $sort_by;
       uksort($object_array, array($this, "_comp"));
   }
}

// Sort array of arrays by key
class t_array_sorter {
   var $array_array;
   var $sort_by;
  
   function _comp($a, $b) {
       $key = $this->sort_by;
       if ($this->array_array[$a][$key] == $this->array_array[$b][$key]) return 0;
       return ($this->array_array[$a][$key] < $this->array_array[$b][$key]) ? -1 : 1;
   }
  
   function sort(&$array_array, $sort_by) {
       $this->array_array = $array_array;
       $this->sort_by = $sort_by;
       uksort($array_array, array($this, "_comp"));
   }
}
?>