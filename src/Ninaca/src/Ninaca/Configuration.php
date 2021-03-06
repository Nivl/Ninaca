<?php

/*!
**  \file	Configuration.php
**  \author	Nivl <nivl@free.fr>
**  \started	04/30/2010, 05:08 PM
**  \last	Nivl <nivl@free.fr> 06/07/2010, 02:05 AM
**  \copyright	Copyright (C) 2009 Laplanche Melvin
**  
**  Licensed under the MIT license:
**  <http://www.opensource.org/licenses/mit-license.php>.
**  For the full copyright and license information, please view the LICENSE
**  file that was distributed with this source code.
*/


namespace Ninaca;
use \Ninaca\Exceptions\InvalidArgumentException as Iae;
use \Ninaca\Exceptions\FtpException;
use \Ninaca\Utilities\Arrays;
use \Ninaca\Utilities\Debug;


/*!
** Configuration class
*/
class Configuration implements \ArrayAccess, \Iterator
{
  protected
    $_config = array(), ///< \c array
    $_map    = array(), ///< \c array - Type of each options.
    $_vars   = array(), ///< \c array - List of variables and their values. 
    $_loadedFiles = array(); ///< \c array list of loaded files.
  

  /*!
  ** Constructor
  **
  ** \param [file]
  **          \c string - file to load.
  ** \param [vars]
  **          \c array - Array of variables and their values
  **
  ** \throw Ninaca\Exceptions\InvalidArgumentTypeException
  **     if \a file is not a string.
  */
  public function __construct($file = '',
			      array $vars = array())
  {
    Debug::checkArgs(0,
		     1, 'string', $file);
    
    $this->_vars = $vars;
    if ($file !== '')
      $this->loadFile($file);
  }
  
  
  /*!
  ** Load a file and merge it with the current configuration.
  **
  ** \param file
  **          \c string - file to load.
  ** \param [vars]
  **          \c array - Array of variables and their values
  **
  ** \throw Ninaca\Exceptions\InvalidArgumentTypeException
  **     if \a file is not a string or is empty.
  ** \throw Ninaca\Exceptions\FtpException
  **     if \a file is not readable.
  */
  public function loadFile($file,
			   array $vars = array())
  {
    Debug::checkArgs(0,
		     1, 'string', $file,
		     1, 'nonempty', $file);
    
    $file = realpath($file);
    if (!$file || !is_readable($file) || !is_file($file))
      throw new FtpException("The file $file is not readable.");
    if (!in_array($file, $this->_loadedFiles)) {
      $this->_loadedFiles[] = $file;
      $ext  = pathinfo($file, PATHINFO_EXTENSION);
      if ($ext === 'yml' || $ext === 'yaml')
	$conf = Parsers\Yaml\Factory::factory()->loadFile($file);
      if ($ext === 'ini')
	$conf = parse_ini_file($file, true);
      else if ($ext === 'php')
	$conf = include $file;
      $this->_importFiles($conf);
      if ($this->_vars || $vars)
	$conf = $this->parseConf($conf, $vars ? $vars : $this->_vars);
      if (!empty($this->_config))
	$this->_config = Arrays::arrayMergeRec($this->_config, $conf);
      else
	$this->_config = $conf;}
  }
  
  
  /*!
  ** Import several files.
  **
  ** \param &conf
  **          \c array - configuration’s array.
  */
  protected function _importFiles(array &$conf)
  {
    if (!empty($conf['import']) && is_array($conf['import'])) {
      foreach ($conf['import'] as $file) {
	if (!empty($file)) {
	  if (!is_readable($file) || !is_file($file))
	    throw new FtpException("The file $file is not readable.");
	  $this->loadFile($file);}}}
    unset($conf['import']);
  }
  
  
  /*!
  ** Parse a configuration.
  **
  ** \param conf
  **          \c array - Configuration.
  **
  ** \return \c array
  */
  protected function parseConf(array $conf, array $vars)
  {
    $name   = array_keys($vars);
    $values = array_values($vars);

    foreach ($conf as &$value){
      if (is_array($values))
	$values = $this->parseConf($values);
      else
	$values = str_replace($name, $values, $values);}
    return $conf;
  }
  
  
  /*!
  ** returns the configuration.
  **
  ** \return \c array
  */
  public function getConf()
  {
    return $this->_config;
  }


  /*!
  ** Checks if a key exists in the configuration.
  ** You can use an array to check deeper.
  **
  ** \param offset
  **          \c string|array
  **
  ** \throw Ninaca\Exceptions\InvalidArgumentTypeException
  **     if \a offset is not an array or a string, or is empty.
  **
  ** \usage
  **    isset($Object['key']);
  **    isset($Object[array('key', 'subkey')]);
  ** \endusage
  **
  ** \return \c bool
  */
  public function offsetExists($offset)
  {
    Debug::checkArg(0,
		    1, 'array or string', $offset,
		    1, 'nonempty', $offset);
    
    if (is_array($offset)) {
      $config =& $this->_config;
      foreach ($offset as $key) {
	if (!isset($config[$key]))
	  return false;
	else
	  $config =& $config[$key];}
      return true; }
    else
      return isset($this->_config[$offset]);
  }


  /*!
  ** Return the value of a key.
  **
  ** \param offset
  **          \c string
  **
  ** \throw Ninaca\Exceptions\InvalidArgumentTypeException
  **     if \a offset is not a string, or is empty.
  ** \throw Ninaca\Exceptions\InvalidArgumentException
  **     if \a offset doens’t exists.
  **
  ** \return \c mixed
  */
  public function offsetGet($offset)
  {
    Debug::checkArg(0,
		    1, 'string', $offset,
		    1, 'nonempty', $offset); 
    
    if (isset($this->_config[$offset]))
      $this->_config[$offset];
    throw new Iae("Undefined index: $offset");
  }
  
  
  /*!
  ** Set a value to a key.
  ** You can use an array to set deeper.
  **
  ** \param offset
  **          \c string|array
  ** \param value
  **          \c mixed
  **
  ** \throw Ninaca\Exceptions\InvalidArgumentTypeException
  **     if \a offset is not a string, or is empty.
  ** \throw Ninaca\Exceptions\InvalidArgumentException
  **     if \a offset doens’t exists.
  */
  public function offsetSet($offset,
			    $value)
  {
    Debug::checkArg(0,
		    1, 'array or string', $offset,
		    1, 'nonempty', $offset); 
    
    if (is_array($offset)) {
      $config =& $this->_config;
      foreach ($offset as $key) {
	if (!isset($config[$key]))
	  throw new Iae("Undefined index: $key");
	else
	  $config =& $config[$key];}
      $config = $value;}
    else {
      if (isset($this->_config[$offset]))
	$this->_config[$offset] = $value;
      else
	throw new Iae("Undefined index: $offset");}
  }
  
  
  /*!
  ** Unset a key.
  ** You can use an array to unset deeper.
  **
  ** \param offset
  **          \c string|array
  **
  ** \throw Ninaca\Exceptions\InvalidArgumentTypeException
  **     if \a offset is not a string, or is empty.
  ** \throw Ninaca\Exceptions\InvalidArgumentException
  **     if \a offset doens’t exists.
  */
  public function offsetUnset($offset)
  {
    Debug::checkArg(0,
		    1, 'array or string', $offset,
		    1, 'nonempty', $offset); 
    
    if (is_array($offset)) {
      $config =& $this->_config;
      foreach ($offset as $key) {
	if (!isset($config[$key]))
	  throw new Iae("Undefined index: $key");
	else if ($key == end($offset))
	  unset($config[$key]);
	else
	  $config =& $config[$key];}}
    else {
      if (isset($this->_config[$offset]))
	unset($this->_config[$offset]);
      else
	throw new Iae("Undefined index: $offset");}
  }
  
  
  /*!
  ** Rewinds iterator.
  */
  public function rewind()
  {
    begin($this->_config);
  }
  
  
  /*!
  ** Valids iterator.
  */
  public function valid()
  {
    key($this->_config) !== null;
  }
  
  
  /*!
  ** Returns current value.
  */
  public function current()
  {
    return current($this->_config);
  }
  
  
  /*!
  ** Returns current key.
  */
  public function key()
  {
    return key($this->_config);
  }
  
  
  /*!
  ** Moves forward the iterator.
  */
  public function next()
  {
    return next($this->_config);
  }
}

