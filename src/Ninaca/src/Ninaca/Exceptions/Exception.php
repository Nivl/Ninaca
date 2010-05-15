<?php

/*!
**  \file	Exception.php
**  \author	Nivl <nivl@free.fr>
**  \started	04/18/2010, 01:13 AM
**  \last	Nivl <nivl@free.fr> 04/24/2010, 01:24 AM
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
** Main class to throw exceptions.
*/
Class Exception extends \Exception
{
  protected $real_message = ''; ///< \c string - Message without debuging info.

  /*!
  ** Constructor
  **
  ** \param msg
  **         \c string
  ** \param [search_depth]
  **         \c int - Depth for whoCalledMe().
  ** \param [code]
  **          \c int
  ** \param [Previous]
  **          \c \Exception
  */
  public function __construct($msg,
			      $search_depth = 0,
			      $code = 0,
			      $Previous = NULL)
  {
    if (!is_string($msg))
      throw new InvalidArgumentException(1, 'string', gettype($msg));
    if ($msg === '')
      throw new InvalidArgumentException(1, 'nonempty string', 'empty string');
    if (!is_int($search_depth) && !ctype_digit($search_depth))
      throw new InvalidArgumentException(2, 'int', gettype($search_depth));
    if (!is_int($code) && !ctype_digit($code))
      throw new InvalidArgumentException(3, 'int', gettype($code));
    if ($Previous !== NULL && !($Previous instanceof \Exception)) {
      $type = is_object($Previous) ? get_class($Previous) : gettype($Previous);
      throw new InvalidArgumentException(4,'NULL or an instance of \Exception',
					 $type);}
    
    $this->real_message = $msg;
    $infos = \Ninaca\Utilities\Debug::whoCalledMe($search_depth);
    $class = &$infos['class'];
    $func  = &$infos['function'];
    $file  = &$infos['file'];
    $line  = &$infos['line'];
    $class_func = !empty($class) ? "$class::$func()" : "$func()";
    $msg = "$class_func: $msg <br/>".
      "This function has been called in $file at line $line.<br />".
      "This exception has been thrown";
    parent::__construct($msg, $code, $Previous);
  }
  
  
  /*!
  ** Return the message without the debuging informations.
  **
  ** \return \c string
  */
  public function getRealMessage()
  {
    return $this->real_message;
  }
}