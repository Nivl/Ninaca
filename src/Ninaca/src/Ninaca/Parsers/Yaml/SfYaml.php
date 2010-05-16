<?php

/*!
**  \file	SfYaml.php
**  \author	Nivl <nivl@free.fr>
**  \started	03/31/2010, 03:11 PM
**  \last	Nivl <nivl@free.fr> 04/28/2010, 05:57 PM
**  \copyright	Copyright (C) 2009 Laplanche Melvin
**  
**  Licensed under the MIT license:
**  <http://www.opensource.org/licenses/mit-license.php>.
**  For the full copyright and license information, please view the LICENSE
**  file that was distributed with this source code.
*/


namespace Ninaca\Parsers\Yaml;
use \Symfony\Components\Yaml\Yaml as ScYaml;
use \Ninaca\Exceptions\FtpException;
use \Ninaca\Exceptions\ParserException;
use \Ninaca\Exceptions\InvalidArgumentException;


/*!
** Parses YAML file with Symfony’s class.
*/
class SfYaml implements Yaml
{
  /*!
  ** Constructor.
  */
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
  ** \throw Ninaca\Exceptions\ParserException
  **     if the dump fails.
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
      throw new InvalidArgumentException(2,'string or array',gettype($yaml));
    
    if (is_array($yaml)) {
      try {
	$yaml = ScYaml::dump($yaml);} 
      catch (\Symfony\Components\Yaml\Exception $e) {
	throw new ParserException($e->getMessage());}}
    try {
      file_put_contents($file, $yaml, LOCK_EX);}
    catch (\ErrorException $e) {
      throw new FtpException(mb_substr(mb_strstr($e->getMessage(), ']:'),3));}
  }
  
  
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
  public function dump(array $array)
  {
    try {
      return ScYaml::dump($array);}
    catch (\Symfony\Components\Yaml\Exception $e) {
      throw new ParserException($e->getMessage());}
  }
  
  
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
  public function load($yaml)
  {
    if (!is_string($yaml))
      throw new InvalidArgumentException(1, 'string', gettype($yaml));
    
    if (strpos($yaml, "\n") === false && is_file($yaml))
      return $this->loadFile($yaml);
    try {
      return ScYaml::load($yaml);}
    catch (\Symfony\Components\Yaml\ParserException $e) {
      throw new ParserException($e->getMessage());}
    catch (\InvalidArgumentException $e) {
      throw new ParserException($e->getMessage());}
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
  ** \throw Ninaca\Exceptions\ParserException
  **     if the parsing fails.
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
     try {
      return ScYaml::load($file);}
    catch (\Symfony\Components\Yaml\ParserException $e) {
      throw new ParserException($e->getMessage());}
    catch (\InvalidArgumentException $e) {
      throw new ParserException($e->getMessage());}
  }
}


