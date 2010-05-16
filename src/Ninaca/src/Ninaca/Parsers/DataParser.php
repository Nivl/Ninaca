<?php

/*!
**  \file	DataParser.php
**  \author	Nivl <nivl@free.fr>
**  \started	04/28/2010, 04:17 PM
**  \last	Nivl <nivl@free.fr> 04/28/2010, 05:10 PM
**  \copyright	Copyright (C) 2009 Laplanche Melvin
**  
**  Licensed under the MIT license:
**  <http://www.opensource.org/licenses/mit-license.php>.
**  For the full copyright and license information, please view the LICENSE
**  file that was distributed with this source code.
*/


namespace Ninaca\Parsers;


/*!
** Interface for data parser (yaml, xml, …).
*/
interface DataParser
{
  public function store($file, $data);
  public function dump(array $array);
  public function load($sdata);
  public function loadFile($file);
}

