<?php

/*!
**  \file	Autoload.php
**  \author	Nivl <nivl@free.fr>
**  \started	03/31/2010, 04:52 PM
**  \last	Nivl <nivl@free.fr> 04/21/2010, 11:11 PM
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


namespace Ninaca;
use Ninaca\Exceptions\Exception;
use Ninaca\Exceptions\InvalidArgumentException;
use Ninaca\Exceptions\RuntimeException;


/*!
** Class used to autoload the others classes and interfaces.
*/
class Autoload
{
  protected
    $_namespaces = array(), ///< \c array - List of defined namespaces.
    $_prefixes	 = array(), ///< \c array - List of defined prefixes.
    $_links	 = array(), ///< \c array - List of defined links.
    $_check_path = true,    ///< \c bool  - Check paths on the fly?
    $_started	 = false;   ///< \c bool  - Define the state of the autoloader.

  static private
    $__instances = array(), ///< \c array - User-defined instances.
    $__Instance	 = null;     ///< \c Autoload - default instance.


  /*!
  ** Get the value of a protected attribute.
  **
  ** The readable attributes are:
  **   \li check_path
  **
  ** \param att
  **          \c string - Attribute name.
  **
  ** \return \c mixed - false if \a att is not readable.
  */
  public function __get($att)
  {
    return $att == 'check_path' ? $this->_check_path : false;
  }
  
  
  /*!
  ** Set a value to a protected attribute.
  **
  ** The writable attributes are:
  **   \li check_path
  **
  ** \param att
  **          \c string - Attribute’s name.
  ** \param value
  **          \c mixed - Attribute’s value.
  **
  ** \return \c bool - false if the attribute is not writable, else false.
  */
  public function __set($att, $value)
  {
    if ($att == 'check_path')
      $this->_check_path  = (bool)$value;
    else
      return false;
    return true;
  }
  
  
  /*!
  ** Return an instance of the class.
  **
  ** \param [id]
  **          \c string|null - Instance’s id (if you use several instances).
  **
  ** \return \c Autoload
  */
  static public function getInstance($id = null)
  {
    if ($id !== null && !is_string($id))
      throw new InvalidArgumentException(1, 'string or null', gettype($id));

    $class = __CLASS__;
    if ($id === null) {
      if (self::$__Instance === null)
	self::$__Instance = new $class();
      return self::$__Instance;}
    else{
      if (!isset(self::$__instances[$id]))
	self::$__instances[$id] = new $class();
      return self::$__instances[$id];}
  }
  
  
  /*!
  ** Adds namespaces
  **
  ** \param namespaces
  **          \c array - [name] => p/a/t/h.
  **
  ** \throw Ninaca\Exceptions\InvalidArgumentException
  **     if a key or a path is not a string.
  ** \throw Ninaca\Exceptions\Exception
  **     if a namespace has already been defined.
  ** \throw Ninaca\Exceptions\Exception
  **     if a path doesn’t exists.
  */
  public function addNamespaces(array $namespaces)
  {
    foreach ($namespaces as $ns => $path) {
      if (!is_string($ns))
	throw new InvalidArgumentException(1, 'array with string as key',
					   gettype($ns));
      if (!is_string($path))
	throw new InvalidArgumentException(1, 'array with string as value',
					   gettype($path));
      if (isset($this->_namespaces[$ns]))
	throw new Exception("The namespace $ns has already been defined.");
      else if ($this->_check_path && !is_dir($path))
	throw new Exception("The path $path doesn’t exists.");
      
      $this->_namespaces[$ns] = $path;}
  }
  
  
  /*!
  ** Adds a namespace.
  **
  ** \param ns
  **          \c string
  ** \param path
  **          \c string
  **
  ** \throw Ninaca\Exceptions\InvalidArgumentException
  **     if a key or a path is not a string.
  ** \throw Ninaca\Exceptions\Exception
  **     if a namespace has already been defined.
  ** \throw Ninaca\Exceptions\Exception
  **     if a path doesn’t exists.
  */
  public function addNamespace($ns,
			       $path)
  {
    if (!string($ns))
      throw new InvalidArgumentException(1, 'string', gettype($ns));
    if (!string($path))
      throw new InvalidArgumentException(2, 'string', gettype($path));
    if (isset($this->_namespaces[$ns]))
      throw new Exception("The namespace $ns has already been defined.");
    else if ($this->_check_path && !is_dir($path))
      throw new Exception("The path $path doesn’t exists.");
    
    $this->_namespaces[$ns] = $path;
  }

  
  /*!
  ** Adds prefixes.
  **
  ** \param prefixes
  **          \c array - [name] => p/a/t/h.
  **
  ** \throw Ninaca\Exceptions\InvalidArgumentException
  **     if a key or a path is not a string.
  ** \throw Ninaca\Exceptions\Exception
  **     if a prefix has already been defined.
  ** \throw Ninaca\Exceptions\Exception
  **     if a path doesn’t exists.
  */
  public function addPrefixes(array $prefixes)
  {
    foreach ($prefixes as $prefix => $path) {
      if (!string($prefix))
	throw new InvalidArgumentException(1, 'array with string as key',
					 gettype($prefix));
      if (!string($path))
	throw new InvalidArgumentException(1, 'array with string as value',
					   gettype($path));
      if (isset($this->_prefixes[$prefix]))
	throw new Exception("The prefix $prefix has already been defined.");
      else if ($this->_check_path && !is_dir($path))
	throw new Exception("$path doesn’t exists.");
      
      $this->_prefixes[$prefix] = $path;}
  }
  
  
  /*!
  ** Adds a prefix.
  **
  ** \param prefix
  **          \c string 
  ** \param path
  **          \c string
  **
  ** \throw Ninaca\Exceptions\InvalidArgumentException
  **     if a key or a path is not a string.
  ** \throw Ninaca\Exceptions\Exception
  **     if a prefix has already been defined.
  ** \throw Ninaca\Exceptions\Exception
  **     if a path doesn’t exists.
  */
  public function addPrefix($prefix,
			    $path)
  {
    if (!string($prefix))
      throw new InvalidArgumentException(1, 'string', gettype($prefix));
    if (!string($path))
      throw new InvalidArgumentException(2, 'string', gettype($path));
    if (isset($this->_prefixes[$prefix]))
      throw new Exception("The prefix $prefix has already been defined.");
    else if ($this->_check_path && !is_dir($path))
      throw new Exception("$path doesn’t exists.");

    $this->_prefixes[$prefix] = $path;
  }


  /*!
  ** Adds links.
  ** Links are used to link a class or interface to a file. It’s useful if
  ** you put several classes or interfaces in the same file.
  **
  ** \param links
  **          \c array - [class/interface's name with namespace] => f/i/l/e.
  **
  ** \throw Ninaca\Exceptions\InvalidArgumentException
  **     if a key or a file is not a string or if the filename is empty.
  ** \throw Ninaca\Exceptions\Exception
  **     if a class/interface has already been defined.
  ** \throw Ninaca\Exceptions\Exception
  **     if a file doesn’t exists.
  */
  public function addLinks(array $links)
  {
    foreach ($links as $class => $file) {
      if (!string($class))
	throw new InvalidArgumentException(1, 'array with string as key',
					   gettype($class));
      if (!string($file))
	throw new InvalidArgumentException(1, 'array with string as value',
					   gettype($file));
      if ($file === '')
	throw new InvalidArgumentException(
	  1, 'array with nonempty string as value', 'empty string');
      if (isset($this->_links[$class]))
	throw new Exception("The $link $class has already been defined.");
      else if ($this->_check_path && !is_file($file))
	throw new Exception("$file is not a file or doesn’t exists.");
      
      $this->_links[$class] = $file;}
  }
  

  /*!
  ** Adds a link.
  ** Links are used to link a class or interface to a file. It’s useful if
  ** you put several classes or interfaces in the same file.
  **
  ** \param class
  **          \c string - Class or interface's name (with namespace).
  ** \param file
  **          \c string
  **
  ** \throw Ninaca\Exceptions\InvalidArgumentException
  **     if \a file is not a string, or empty.
  ** \throw Ninaca\Exceptions\Exception
  **     if \a class has already been defined.
  ** \throw Ninaca\Exceptions\Exception
  **     if \a file doesn’t exists.
  */
  public function addLink($class, $file)
  {
    if (!string($class))
      throw new InvalidArgumentException(1, 'array with string as key',
					 gettype($class));
    if (!string($file))
      throw new InvalidArgumentException(1, 'array with string as value',
					 gettype($file));
    if ($file === '')
      throw new InvalidArgumentException(1,
					 'array with nonempty string as value',
					 'empty string');
    if (isset($this->_links[$class]))
      throw new Exception("The $link $class has already been defined.");
    else if ($this->_check_path && !is_file($file))
      throw new Exception("$file is not a file or doesn’t exists.");
    
    $this->_links[$class] = $file;
  }

  
  /*!
  ** Checks if the autoloader is started.
  **
  ** \return \c bool
  */
  public function isStarted()
  {
    return $this->_started;
  }
  
  
  /*!
  ** Starts the autoloader
  **
  ** \throw Ninaca\Exceptions\RuntimeException
  **     if the autloader is already started.
  */
  public function start()
  {
    if (!$this->_started) {
      spl_autoload_register(array($this, 'loadClass'));
      $this->_started = true; }
    else
      throw new RuntimeException('The autoloader has already been started.');
  }
  
  
  /*!
  ** Stops the autoloader
  **
  ** \throw Ninaca\Exceptions\RuntimeException
  **     if the autloader is already stopped.
  */
  public function stop()
  {
    if ($this->_started)
    {
      spl_autoload_unregister(array($this, 'loadClass'));
      $this->_started = false;
    }
    else
      throw new RuntimeException('The autoloader hasn’t been started.');
  }
  
  
  /*!
  ** Loads the given class or inteface
  **
  ** \param class
  **          \c string - Class or interface to load.
  */
  public function loadClass($class)
  {
    if (!is_string($class))
      throw new InvalidArgumentException(1, 'string', gettype($class));

    if (isset($this->files[$class]))
      include $this->files[$class];
    else {
      $class = ltrim($class, '\\_');
      $vendor = stristr($class, '\\', true);
      if ($vendor) {
	if (!isset($this->_namespaces[$vendor]))
	  return;
	$last_ns_pos = strripos($class, '\\');
	$namespace   = substr($class, 0, $last_ns_pos);
	$class_name  = substr($class, $last_ns_pos+1);
	$filename    = str_replace('\\', DIRECTORY_SEPARATOR, $namespace);
	$filename    = $filename.DIRECTORY_SEPARATOR.$class_name.'.php';
	$path	   = $this->_namespaces[$vendor];}
      else {
	$vendor = stristr($class, '_', true);
	if (!$vendor || !isset($this->_prefixes[$vendor]))
	  return;
	$filename = str_replace('_', DIRECTORY_SEPARATOR, $namespace).'.php';
	$path = $this->_prefixes[$vendor];}
      include $path.DIRECTORY_SEPARATOR.$filename;}
  }
  
  
  private function __clone(){}
  private function __construct(){}
}