<?php

/*!
**  \file	Factory.php
**  \author	Nivl <nivl@free.fr>
**  \started	08/28/2009, 02:35 AM
**  \last	Nivl <nivl@free.fr> 05/15/2010, 11:05 PM
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


namespace Ninaca\Parsers\Yaml;


/*!
**  This class returns the best class you can use to parse yaml files.
*/
abstract class Factory
{
  static private $__Instance = null;


  /*!
  ** Returns the needed class.
  **
  ** \return \c Ninaca::Parsers::Yaml::Yaml
  */
  static public function getInstance()
  {
    if ( self::$__Instance === null ) {
      $type = self::getType();
      $class_name = __NAMESPACE__."\\{$type}";
      self::$__Instance = new $class_name();}
    return self::$__Instance;
  }
  
  
  /*!
  ** Alias of getInstance.
  **
  ** \see getInstance().
  */
  static public function factory()
  {
    return self::getInstance();
  }
  
  
  /*!
  ** Selects the best class.
  **
  ** \return \c string
  */
  static private function getType()
  {
    if ( extension_loaded('syck') )
      return 'Syck';
    else
      return 'SfYaml';
  }
  
  
  private function __construct(){}
  private function __clone(){}
}


