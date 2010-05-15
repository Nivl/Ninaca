<?php

/*!
**  \file	Yaml.php
**  \author	Nivl <nivl@free.fr>
**  \started	08/29/2009, 10:12 PM
**  \last	Nivl <nivl@free.fr> 04/28/2010, 04:50 PM
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
**  along with this program. If not, see <http://www.gnu.org/licenses/>.
*/


namespace Ninaca\Parsers\Yaml;

/*!
** Main yaml class.
*/
interface Yaml
{
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
  ** \throw Ninaca\Exceptions\ParserException
  **     if the dump fails.
  ** \throw Ninaca\Exceptions\FtpException
  **     if the storage fails.
  */
  abstract public function store($file, $yaml);
  
  
  /*!
  ** Parses an array to a YAML string.
  **
  ** \param array
  **          \c array
  **
  ** \throw Ninaca\Exceptions\ParserException
  **     if the dump fails.
  **
  ** \return \c string
  */
  abstract public function dump(array $array);
  
  
  /*!
  ** Parses a YAML string to an array.
  **
  ** \param yaml
  **          \c string
  **
  ** \throw Ninaca\Exceptions\InvalidArgumentException
  **     if \a yaml is not a string.
  ** \throw Ninaca\Exceptions\ParserException
  **     if the parsing fails.
  **
  ** \return \c array
  */
  abstract public function load($yaml);
  
  
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
  ** \throw Ninaca\Exceptions\ParserException
  **     if the parsing fails.
  **
  ** \return \c array
  */
  abstract public function loadFile($file);
}
