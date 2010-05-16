<?php

/*!
**  \file	Factory.php
**  \author	Nivl <nivl@free.fr>
**  \started	12/20/08
**  \last	Nivl <nivl@free.fr> 05/15/2010, 11:35 PM
**  \copyright	Copyright (C) 2008 Laplanche Melvin
**
**  Licensed under the MIT license:
**  <http://www.opensource.org/licenses/mit-license.php>.
**  For the full copyright and license information, please view the LICENSE
**  file that was distributed with this source code.
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

