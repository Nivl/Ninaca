<?php

/*!
**  \file	SfYaml.php
**  \author	Nivl <nivl@free.fr>
**  \started	03/31/2010, 03:11 PM
**  \last	Nivl <nivl@free.fr> 06/07/2010, 01:49 AM
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
use \Ninaca\utilities\Debug;


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
  ** \throw Ninaca\Exceptions\InvalidArgumentTypeException
  **     if \a file is not a string or is empty.
  ** \throw Ninaca\Exceptions\InvalidArgumentTypeException
  **     if \a yaml is not a string or array.
  ** \throw Ninaca\Exceptions\ParserException
  **     if the dump fails.
  ** \throw Ninaca\Exceptions\FtpException
  **     if the storage fails.
  */
  public function store($file,
			$yaml)
  {
    Debug::checkArgs(0,
		     1, 'string', $file,
		     1, 'nonempty', $file,
		     2, 'string or array', $yaml);
    
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
  ** \throw Ninaca\Exceptions\InvalidArgumentTypeException
  **     if \a yaml is not a string.
  ** \throw Ninaca\Exceptions\ParserException
  **     if the parsing fails.
  **
  ** \return \c array
  */
  public function load($yaml)
  {
    Debug::checkArgs(0,
		     1, 'string', $yaml);
    
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
  ** \throw Ninaca\Exceptions\InvalidArgumentTypeException
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
    Debug::checkArgs(0,
		     1, 'string', $file,
		     1, 'nonempty', $file);

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


