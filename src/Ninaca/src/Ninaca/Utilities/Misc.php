<?php

/*!
**  \file	Misc.php
**  \author	Nivl <nivl@free.fr>
**  \started	07/17/2009, 12:01 AM
**  \last	Nivl <nivl@free.fr> 05/01/2010, 12:23 AM
**  \copyright	Copyright (C) 2009 Laplanche Melvin
**  
**  Licensed under the MIT license:
**  <http://www.opensource.org/licenses/mit-license.php>.
**  For the full copyright and license information, please view the LICENSE
**  file that was distributed with this source code.
*/


namespace Ninaca\Utilities;


/*!
** Miscellaneous functions.
*/
class Misc
{  
  /*!
  ** Checks if the given argument is empty.
  **
  ** These folowing values are considered as empty:
  **   \li \e '' (Empty string)
  **   \li \e false
  **   \li \e null
  **   \li \e array()
  **
  ** An array which has keys but with empty values is considered as empty
  ** (if the value of a key is an array, it will be checked the same way).
  **
  ** \param arg
  **          \c mixed
  **
  ** \return \c bool
  */
  static public function isEmpty($arg)
  {
    if (is_array($arg)) {
      foreach ($arg as $value) {
	if (is_array($value))
	  if (!self::isEmpty($value))
	    return false;
	  else if ($arg)
	    return false;}
      return true;}
    else
      return in_array($arg, array(false, null, ''));
  }


  /*!
  ** Strip whitespace, null-byte, tab, ect. from the begining and the end of
  ** a string.
  **
  ** \param str
  **          \c string
  **
  ** \throw Ninaca\Exceptions\InvalidArgumentException
  **     if \a str isn’t a string.
  **
  ** \return \c string
  */
  static public function trim($str)
  {
    if (!is_string($str))
      throw new InvalidArgumentException(1, 'string', gettype($str));
    
    return trim($str, " \t\n\r\0\x7f..\xff\x0..\x1f");
  }
  
  
  /*!
  ** Checks if the parametre is or contains an integer.
  **
  ** \param var
  **          \c mixed
  **
  ** \return \c bool
  */
  static public function isInt($var)
  {
    return is_int($var) || ctype_digit($var);
  } 
}




