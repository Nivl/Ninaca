<?php

/*!
**  \file	Configuration.php
**  \author	Nivl <nivl@free.fr>
**  \started	04/30/2010, 05:08 PM
**  \last	Nivl <nivl@free.fr> 05/16/2010, 03:00 AM
**  \copyright	Copyright (C) 2009 Laplanche Melvin
**  
**  Licensed under the MIT license:
**  <http://www.opensource.org/licenses/mit-license.php>.
**  For the full copyright and license information, please view the LICENSE
**  file that was distributed with this source code.
*/


namespace Ninaca;
use \Ninaca\Exceptions\InvalidArgumentException;
use \Ninaca\Exceptions\FtpException;


/*!
** Configuration class
*/
class Configuration
{
  protected
    $_config = array(), ///< \c array
    $_map = array(),    ///< \c array - Type of each options.
    $_vars = array();   ///< \c array - List of variables and their values. 
  
  
  /*!
  ** Constructor
  **
  ** \param file
  **          \c string - file to load.
  ** \param vars
  **          \c array - Array of variables and their values
  */
  public function __construct($file = '', array $vars = array())
  {
    Debug::checkArgs(0,
		     1, 'string', $file);
    
    $this->_vars = $vars;
    if ($file !== '')
      $this->loadFile($file);
  }
  
  
  /*!
  ** Load a file
  **
  ** \param file
  **          \c string - file to load.
  **
  ** \throw Ninaca\Exceptions\InvalidArgumentException
  **     if \a file is not a string.
  ** \throw Ninaca\Exceptions\FtpException
  **     if \a file is not readable.
  **
  ** \return \c - 
  */
  public function loadFile($file)
  {
    Debug::checkArgs(0,
		     1, 'string', $file,
		     1, 'nonempty', $file);
    
    $ext = pathinfo($file, PATHINFO_EXTENSION);
    
    if ($ext === 'yml' || $ext === 'yaml')
      $conf = Parser\Yaml\YamlFactory()->loadFile($file);
    if ($ext === 'ini')
      $conf = parse_ini_file($file, true);
    else if ($ext === 'php')
      $conf = include $file;
    
    if ($this->_vars)
      $config = $this->parseConf($conf);
  }
  
  
  /*!
  ** Parse a configuration.
  **
  ** \param conf
  **          \c array - Configuration.
  **
  ** \return \c array
  */
  protected function parseConf(array $conf)
  {
    $name   = array_keys($this->vars);
    $values = array_values($this->vars);

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
  protected function getConf()
  {
    return $this->_config;
  }
}

