<?php

/*!
**  \file	InvalidArgumentException.php
**  \author	Nivl <nivl@free.fr>
**  \started	04/16/2010, 07:16 PM
**  \last	Nivl <nivl@free.fr> 04/20/2010, 01:32 AM
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
**  along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/


namespace Ninaca\Exceptions;


/*!
** Exception thrown if an argument does not match with the expected value.
*/
class InvalidArgumentException extends \InvalidArgumentException
{
  /*!
  ** Constructor
  **
  ** \param param_num
  **          \c int
  ** \param expected
  **          \c string
  ** \param given
  **          \c string
  ** \param search_depth
  **           \c int - Depth for whoCalledMe().
  ** \param code
  **           \c int
  ** \param Previous
  **           \c \Exception
  */
  public function __construct($param_num,
			      $expected,
			      $given,
			      $search_depth = 0,
			      $code = 0,
			      \Exception $Previous = NULL)
  {
    $infos	= \Ninaca\Utilities\Debug::whoCalledMe($search_depth);
    $class	= &$infos['class'];
    $func	= &$infos['function'];
    $file	= &$infos['file'];
    $line	= &$infos['line'];
    $class_func	= !empty($class) ? "$class::$func()" : "$func()";
    $msg = "$class_func expects parameter $param_num to be $expected, ".
      "$given given.<br />".
      "This function has been called in $file at line $line.<br />".
      "This exception has been thrown";
    parent::__construct($msg, $code, $Previous);
  }
}