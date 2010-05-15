<?php

/*!
**  \file	Syck.php
**  \author	Nivl <nivl@free.fr>
**  \started	08/28/2009, 11:32 PM
**  \last	Nivl <nivl@free.fr> 05/15/2010, 11:06 PM
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
use \Ninaca\Exceptions\FtpException;
use \Ninaca\Exceptions\ParserException;


/*!
**  Parses YAML file with Syck.
*/
class Syck implements Yaml
{
  public function __construct() {}


  /*!
  ** Stores a YAML string into a file.
  **
  ** \param file
  **          \c string
  ** \param yaml
  **          \c array|string - YAML file, or array to parse.
  **
  ** \throw Ninaca\Exceptions\InvalidArgumentException
  **     if \a file is not a string or is empty.
  ** \throw Ninaca\Exceptions\InvalidArgumentException
  **     if \a yaml is not a string or array.
  ** \throw Ninaca\Exceptions\FtpException
  **     if the storage fails.
  */
  public function store($file,
			$yaml)
  {
    if (!is_string($file))
      throw new InvalidArgumentException(1, 'string', gettype($file));
    if ($file === '')
      throw new InvalidArgumentException(1, 'nonempty', 'empty string');
    if (!is_string($yaml) && !is_array($yaml))
      throw new InvalidArgumentException(2, 'string or array', gettype($yaml));
    
    if (is_array($yaml))
      $yaml = syck_dump($yaml);
    try {
      file_put_contents($file, $value, LOCK_EX);}
    catch (\ErrorException $e) {
      throw new FtpException(mb_substr(,b_strstr($e->getMessage()), ']:'),3);}
  }
  
  
  /*!
  ** Parses an array to a YAML string.
  **
  ** \param array
  **          \c array
  **
  ** \return \c string
  */
  public function dump(array $array)
  {
    return syck_dump($array);
  }
  
  
  /*!
  ** Parses a YAML string to an array.
  **
  ** \param yaml
  **          \c string
  **
  ** \throw Ninaca\Exceptions\InvalidArgumentException
  **     if \a yaml is not a string.
  **
  ** \return \c array
  */
  public function load($yaml)
  {
    if (!is_string($yaml))
      throw new InvalidArgumentException(1, 'string', gettype($yaml));

    if (strpos($yaml, "\n") === false && is_file($yaml))
      return $this->loadFile($yaml);
    return syck_load($yaml);
  }
  

  /*!
  ** Parses a YAML file to an array.
  **
  ** \param file
  **          \c string
  **
  ** \throw Ninaca\Exceptions\InvalidArgumentException
  **     if \a file is not a string or is empty.
  ** \throw Ninaca\Exceptions\FtpException
  **     if \a file is not readable or doesn’t exists.
  **
  ** \return \c array
  */
  public function loadFile($file)
  {
    if (!is_string($file))
      throw new InvalidArgumentException(1, 'string', gettype($file));
    if ($file === '')
      throw new InvalidArgumentException(1, 'nonempty', 'empty string');
    
    if (!is_file($file) || !is_readable($file))
      throw new FtpException("$file is not readable or doesn’t exists.");
    return syck_load($file);
  }
}


