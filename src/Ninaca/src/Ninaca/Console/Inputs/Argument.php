<?php

/*!
**  \file	Argument.php
**  \author	Nivl <nivl@free.fr>
**  \started	10/17/2009, 11:34 PM
**  \last	Nivl <nivl@free.fr> 06/27/2010, 07:08 PM
**  \copyright	Copyright (C) 2009 Laplanche Melvin
**  
**  Licensed under the MIT license:
**  <http://www.opensource.org/licenses/mit-license.php>.
**  For the full copyright and license information, please view the LICENSE
**  file that was distributed with this source code.
*/


namespace Ninaca\Console\Input;


/*!
**  Defines an argument.
*/
class Argument
{
  const REQUIRED = 0x01;
  const OPTIONAL = 0x02;
  
  protected
    $type	 = self::REQUIRED,
    $name	 = null,
    $value	 = null,
    $default	 = null,
    $description = null;
    
    
  public function __construct($name,
			      $description,
			      $type = self::REQUIRED,
			      $default = null)
  {
    $this->name		= $name;
    $this->type		= $type;
    $this->default	= $default;
    $this->description	= _($description);
    
    if ( !in_array($type, array(self::REQUIRED, self::OPTIONAL)) )
      $this->type = self::OPTIONAL;
  }
  
  
  public function setValue($val)
  {
    return $this->value = $val;
  }
  
  
  public function hasValue()
  {
    return $this->value !== null;
  }

  
  public function isRequired()
  {
    return $this->type === self::REQUIRED;
  }
  
  
  public function getName()
  {
    return $this->name;
  }
  
  
  public function getDescription()
  {
    return $this->description;
  }
  
  
  public function getValue()
  {
    return (!empty($this->value)) ? $this->value : $this->default;
  }
}
