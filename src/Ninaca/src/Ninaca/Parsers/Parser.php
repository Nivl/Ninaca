<?php

/*!
**  \file	Parser.php
**  \author	Nivl <nivl@free.fr>
**  \started	04/28/2010, 04:17 PM
**  \last	Nivl <nivl@free.fr> 04/28/2010, 04:29 PM
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


namespace Ninaca\Parsers;


/*!
** Interface for parser class.
*/
interface Parser
{
  public function store($file, $string);
  public function dump(array $array);
  public function load($string);
  public function loadFile($file);
}

