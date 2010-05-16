<?php

/*!
**  \file	Cache.php
**  \author	Nivl <nivl@free.fr>
**  \started	08/29/2009, 10:26 PM
**  \last	Nivl <nivl@free.fr> 04/23/2010, 02:26 AM
**  \copyright	Copyright (C) 2009 Laplanche Melvin
**  
**  Licensed under the MIT license:
**  <http://www.opensource.org/licenses/mit-license.php>.
**  For the full copyright and license information, please view the LICENSE
**  file that was distributed with this source code.
*/


namespace Ninaca\Caches;


/*!
** Interface for caches.
*/
interface Cache
{
  public function getId();
  public function setRootDir($dir);
  public function delete($key);
  public function get($key);
  public function exists($key);
  public function store($key, $var, $lifetime = 0, $overwrite = true);
  public function clearAll();
  public function clear($dir = '', $recursive = false, $pre = '', $suf = '');
}




