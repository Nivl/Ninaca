<?php

/*!
**  \file   	ErrorToException.php
**  \author	Nivl <nivl@free.fr>
**  \started	04/04/2010, 01:52 AM
**  \last	Nivl <nivl@free.fr> 04/19/2010, 02:21 PM
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
