<?php

/*!
**  \file	Exception.php
**  \author	Nivl <nivl@free.fr>
**  \started	04/18/2010, 01:13 AM
**  \last	Nivl <nivl@free.fr> 04/24/2010, 01:24 AM
**  \copyright	Copyright (C) 2009 Laplanche Melvin
**  
**  Licensed under the MIT license:
**  <http://www.opensource.org/licenses/mit-license.php>.
**  For the full copyright and license information, please view the LICENSE
**  file that was distributed with this source code.
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