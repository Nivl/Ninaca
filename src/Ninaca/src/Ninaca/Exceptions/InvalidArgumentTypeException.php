<?php

/*!
**  \file	InvalidArgumentTypeException.php
**  \author	Nivl <nivl@free.fr>
**  \started	04/16/2010, 07:16 PM
**  \last	Nivl <nivl@free.fr> 05/24/2010, 09:39 PM
**  \copyright	Copyright (C) 2009 Laplanche Melvin
**  
**  Licensed under the MIT license:
**  <http://www.opensource.org/licenses/mit-license.php>.
**  For the full copyright and license information, please view the LICENSE
**  file that was distributed with this source code.
*/


namespace Ninaca\Exceptions;


/*!
** Exception thrown if an argument does not match with the expected value.
*/
class InvalidArgumentTypeException extends \InvalidArgumentException
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