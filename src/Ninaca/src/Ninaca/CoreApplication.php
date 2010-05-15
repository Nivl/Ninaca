<?php

/*!
**  \file	CoreApplication.php
**  \author	Nivl <nivl@free.fr>
**  \started	03/30/2010, 10:34 PM
**  \last	Nivl <nivl@free.fr> 04/21/2010, 11:19 PM
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


namespace Ninaca;


/*!
** Create an application.
*/
class CoreApplication
{
  protected
    $_dev = false,    ///< \c bool - Defined if we are in development mode.
    $_start_time = 0; ///< \c int -  Time where the class has been instancied.
  
  
  /*!
  ** Constructor
  **
  ** \param [dev]
  **          \c bool - Set false for production mode.
  */
  public function __construct($dev = false)
  {
    $this->_dev = (bool)$dev;
    if ($dev)
    {
      $this->start = microtime(true);
      init_set('display_error', 1);
      error_reporting(E_ALL | E_STRICT);
    } 
    else
      init_set('display_error', 0);
  }
  
  
  /*!
  ** Return the number of seconds spent since the class has been instancied.
  **
  ** \param [precision]
  **          \c int - The number of decimal digits to round to.
  **
  ** \return \c int
  */
  public function getExecTime($precision = 4)
  {
    if (!is_int($precision) && !ctype_digit($precision))
      throw new InvalidArgumentException(1, 'int', gettype($precision));
    
    return round(microtime(true) - $this->_start_time, $precision)
  }
}