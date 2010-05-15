<?php

/*!
**  \file	Misc.php
**  \author	Nivl <nivl@free.fr>
**  \started	07/17/2009, 12:01 AM
**  \last	Nivl <nivl@free.fr> 04/21/2010, 11:53 PM
**  \copyright	Copyright (C) 2009 Laplanche Melvin
**  
**  This program is free software: you can redistribute it and/or modify
**  it under the terms of the GNU General Public License as published by
**  the Free Software Foundation, either version 3 of the License, or
**  (at your option) any later version.
**
**  This program is distributed in the hope that it will be useful,
**  but WITHOUT ANY WARRANTY; without even the implied warranty of
**  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
**  GNU General Public License for more details.
**
**  You should have received a copy of the GNU General Public License
**  along with this program. If not, see <http://www.gnu.org/licenses/>.
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
}




