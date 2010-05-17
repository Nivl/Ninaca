<?php

/*!
**  \file	SyckYaml.php
**  \author	Nivl <nivl@free.fr>
**  \started	08/28/2009, 11:32 PM
**  \last	Nivl <nivl@free.fr> 05/17/2010, 01:37 PM
**  \copyright	Copyright (C) 2009 Laplanche Melvin
**  
**  Licensed under the MIT license:
**  <http://www.opensource.org/licenses/mit-license.php>.
**  For the full copyright and license information, please view the LICENSE
**  file that was distributed with this source code.
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
  ** \throw Ninaca\Exceptions\InvalidArgumentTypeException
  **     if \a file is not a string or is empty.
  ** \throw Ninaca\Exceptions\InvalidArgumentTypeException
  **     if \a yaml is not a string or array.
  ** \throw Ninaca\Exceptions\FtpException
  **     if the storage fails.
  */
  public function store($file,
			$yaml)
  {
    Debug::checkArg(0,
		    1, 'string', $file,
		    1, 'nonempty', $file,
		    2, 'nonempty', $yaml);
    
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
  ** \throw Ninaca\Exceptions\InvalidArgumentTypeException
  **     if \a yaml is not a string.
  **
  ** \return \c array
  */
  public function load($yaml)
  {
    Debug::checkArg(0,
		    1, 'string', $yaml);

    if (strpos($yaml, "\n") === false && is_file($yaml))
      return $this->loadFile($yaml);
    try {
      return syck_load($yaml); }
    catch (\SyckException $e) {
    throw new ParserException($e->getMessage());}
  }
  

  /*!
  ** Parses a YAML file to an array.
  **
  ** \param file
  **          \c string
  **
  ** \throw Ninaca\Exceptions\InvalidArgumentTypeException
  **     if \a file is not a string or is empty.
  ** \throw Ninaca\Exceptions\FtpException
  **     if \a file is not readable or doesn’t exists.
  **
  ** \return \c array
  */
  public function loadFile($file)
  {
    Debug::checkArg(0,
		    1, 'string', $file,
		    1, 'nonempty', $file);
    
    if (!is_file($file) || !is_readable($file))
      throw new FtpException("$file is not readable or doesn’t exists.");
    try {
      return syck_load($file); }
    catch (\SyckException $e) {
    throw new ParserException($e->getMessage());}
  }
}


