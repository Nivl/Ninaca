<?php

/*!
**  \file	Cache.php
**  \author	Nivl <nivl@free.fr>
**  \started	08/29/2009, 10:26 PM
**  \last	Nivl <nivl@free.fr> 04/23/2010, 02:26 AM
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




