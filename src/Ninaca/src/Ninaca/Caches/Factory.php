<?php

/*!
**  \file	Factory.php
**  \author	Nivl <nivl@free.fr>
**  \started	12/20/08
**  \last	Nivl <nivl@free.fr> 05/15/2010, 11:35 PM
**  \copyright	Copyright (C) 2008 Laplanche Melvin
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


namespace Ninaca\Caches;


/*!
**  This class returns the best class to use to cache data. 
*/
abstract class Factory
{
  static private $__Instance = null;
  
  
  /*!
  ** Return the needed class.
  **
  ** \return \c Ninaca::Caches::Cache
  */
  static public function getInstance()
  {
    if ( self::$__Instance === null )
    {
      $type = self::getType();
      $class_name = __NAMESPACE__."\\{$type}";
      self::$__Instance = new $class_name();
    }
    return self::$__Instance;
  }
  
  
  /*!
  ** Alias of getInstance
  **
  ** \see getinstance().
  */
  static public function factory()
  {
    return self::getInstance();
  }
  
  
  /*!
  ** Select the best class.
  **
  ** \return \c string
  */
  static private function getType()
  {
    if (extension_loaded('apc') && function_exists('apc_add'))
      return 'Apc';
    //else if (extension_loaded('xcache'))
    //return 'XCache';
    else
      return 'File';
  }
  
  
  private function __construct(){}
  private function __clone(){}
}

