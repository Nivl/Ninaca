<?php

/*!
**  \file   	ErrorToException.php
**  \author	Nivl <nivl@free.fr>
**  \started	04/04/2010, 01:52 AM
**  \last	Nivl <nivl@free.fr> 04/19/2010, 02:21 PM
**  \copyright	Copyright (C) 2009 Laplanche Melvin
**  
**  Licensed under the MIT license:
**  <http://www.opensource.org/licenses/mit-license.php>.
**  For the full copyright and license information, please view the LICENSE
**  file that was distributed with this source code.
*/


namespace Ninaca\Exceptions;


/*!
** Convert php errors into exceptions.
*/
abstract class ErrorToException
{
  /*!
  ** Throw an exception.
  **
  ** \param code
  **          \c int -  Will be used as severity, not as code.
  ** \param msg
  **          \c string
  ** \param file
  **          \c  string
  ** \param line
  **          \c string
  **
  ** \throw ErrorException
  */
  static public function error2Exception($code, $msg, $file, $line)
  {
    throw new \ErrorException($msg, 0, $code, $file, $line);
  }
}
