<?php

/*!
**  \file	YamlFactory.php
**  \author	Nivl <nivl@free.fr>
**  \started	08/28/2009, 02:35 AM
**  \last	Nivl <nivl@free.fr> 04/24/2010, 03:14 AM
**  \copyright	Copyright (C) 2009 Laplanche Melvin
**  
**  Licensed under the MIT license:
**  <http://www.opensource.org/licenses/mit-license.php>.
**  For the full copyright and license information, please view the LICENSE
**  file that was distributed with this source code.
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


