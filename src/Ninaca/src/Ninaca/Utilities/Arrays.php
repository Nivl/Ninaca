<?php

/*!
**  \file	Arrays.php
**  \author	Nivl <nivl@free.fr>
**  \started	05/24/2010, 06:40 PM
**  \last	Nivl <nivl@free.fr> 06/07/2010, 01:55 AM
**  \copyright	Copyright (C) 2009 Laplanche Melvin
**  
**  Licensed under the MIT license:
**  <http://www.opensource.org/licenses/mit-license.php>.
**  For the full copyright and license information, please view the LICENSE
**  file that was distributed with this source code.
*/


namespace Ninaca\Utilities;


/*!
** class to manipulate arrays.
*/
class Arrays
{
  const PAD_RIGHT = 1;
  const PAD_LEFT  = 2;


  /*
  ** Pads the keys of the array depending of the biggest key.
  **
  ** \param array
  **          \c array - Array to modify.
  ** \param [str]
  **          \c char - Char used to fill with.
  ** \param [direction]
  **          \c int - PAD_LEFT to fill from the left, PAD_RIGHT for the right.
  **
  ** \throw Ninaca\Exceptions\InvalidArgumentTypeException
  **     if \a str is empty or is not a char.
  **
  ** \return array
  */
  static public function padKeys(array $array,
				 $str = ' ',
				 $direction = self::PAD_RIGHT)
  {
    Debug::checkArgs(0,
		     2, 'char', $str,
		     2, 'nonempty', $str);
    
    $newArray = array();
    $max = self::biggestKeysSize($array);
    foreach ( $array as $key => $value ){
      if ($direction == self::PAD_LEFT)
	$key = str_repeat($str, ($max - mb_strlen($key))).$key;
      else
	$key .= str_repeat($str, ($max - mb_strlen($key)));
      $newArray[$key] = $value; }
    return $newArray;
  }
  
  
  
  /*
  ** Returns the length of the biggest key of an array.
  **
  ** \param array
  **          \c array
  **
  ** \return int
  */
  static public function biggestKeysSize(array $array)
  {
    $max = 0;
    foreach ( $array as $key => $value )
      $max = (mb_strlen($key) > $max) ? mb_strlen($key) : $max;
    return $max;
  }


  /*!
  ** Checks if an value is a multi-dimensional array.
  **
  ** \param array
  **          \c mixed
  **
  ** \return \c bool
  */
  static public function isMultiDim($array)
  {
    if (is_array($array)) {
      foreach ($array as $value) {
	if (is_array($value))
	  return true;}}
    return false;
  }
  
  
  /*!
  ** merges array recursivelly.
  ** non-multi-dimentional array values are overwritten.
  **
  ** \param array1
  **          \c array
  ** \param array2
  **          \c array
  ** \param […]
  **          \c array
  **
  ** \return \c array
  */
  static public function arrayMergeRec(array $array1,
				       array $array2)
  {
    $nb_arg = func_num_args();
    $args   = func_get_args();
    $new_array = self::arrayMergeRec_merge($array1, $array2);
    for ($i=2; $i<$nb_arg; ++$i)
      $new_array = self::arrayMergeRec_merge($new_array, $args[1]);
    return $new_array;
  }


  /*!
  ** merges array recursivelly.
  ** This method is a part of the arrayMergeRecursif method.
  **
  ** \param array1
  **          \c array
  ** \param array2
  **          \c array
  ** \param […]
  **          \c array
  **
  ** \return \c array
  */
  static private function arrayMergeRec_merge(array $array1,
					      array $array2)
  {
    foreach ($array2 as $key => $value) {
      if (!isset($array1[$key]) ||
	  (!self::isMultiDim($value) || !self::isMultiDim($array1[$key])))
	$array1[$key] = $value;
      else
	$array1[$key] = self::arrayMergeRec_merge($array1[$key],$value);}
    return $array1;
  }
}
  
