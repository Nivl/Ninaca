<?php

/*!
**  \file	Option.php
**  \author	Nivl <nivl@free.fr>
**  \started	09/02/2009, 04:57 PM
**  \last	Nivl <nivl@free.fr> 07/06/2010, 07:12 PM
**  \copyright	Copyright (C) 2009 Laplanche Melvin
**
**  Licensed under the MIT license:
**  <http://www.opensource.org/licenses/mit-license.php>.
**  For the full copyright and license information, please view the LICENSE
**  file that was distributed with this source code.
*/


namespace Ninaca\Console\Input;


/*!
** Defines an option.
*/
class Option
{
  const BOOL = 0x01;
  const TEXT = 0x02;

  protected
    $type	 = self::TEXT,
    $name	 = null,
    $value	 = null,
    $default	 = null,
    $shortcut	 = null,
    $description = null;

  public function __construct($name,
			      $description,
			      $default,
			      $shortcut = null,
			      $type = self::TEXT)
  {
    $this->name	= $name;
    $this->description = _($description);
    $this->type = $type;
    $this->default = $default;
    $this->shortcut = $shortcut;
    if (!in_array($type, array(self::BOOL, self::TEXT)))
      $this->type = self::TEXT;
  }


  public function setValue($val)
  {
    return $this->value = $val;
  }


  public function hasValue()
  {
    return $this->value !== null;
  }


  public function hasShortcut()
  {
    return $this->shortcut !== null;
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
    return $this->value ?: $this->default;
  }


  public function getShortcut()
  {
    return $this->shortcut;
  }


  public function isBool()
  {
    return $this->type === self::BOOL;
  }
}


