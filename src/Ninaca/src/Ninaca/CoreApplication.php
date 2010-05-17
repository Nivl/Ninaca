<?php

/*!
**  \file	CoreApplication.php
**  \author	Nivl <nivl@free.fr>
**  \started	03/30/2010, 10:34 PM
**  \last	Nivl <nivl@free.fr> 05/17/2010, 01:01 PM
**  \copyright	Copyright (C) 2009 Laplanche Melvin
**  
**  Licensed under the MIT license:
**  <http://www.opensource.org/licenses/mit-license.php>.
**  For the full copyright and license information, please view the LICENSE
**  file that was distributed with this source code.
*/


namespace Ninaca;
use Ninaca\Configuration as Config;
use Ninaca\Exceptions\Exception;
use Ninaca\Exceptions\InvalidArgumentException;
use Ninaca\Exceptions\RuntimeException;


/*!
** Main class to create application.
*/
abstract class CoreApplication
{
  protected
    $_env        = '', ///< \c string - Defined the environement (prod, dev, …)
    $_start_time = 0,   ///< \c int - Time where the class has been instancied.
    $_configs    = array(), ///< \c array - Array of \Ninaca\Config
    $_debug      = false;   ///< \c bool if the debug mode should be enabled.
  
  
  /*!
  ** Returns the application’s directory (ex: /var/www/MyWebsite/apps/Myapp).
  **
  ** \return \c string
  */
  abstract protected function getApplicationDir();
  
  
  /*!
  ** Returns the path of the file which contains the routes.
  **
  ** \return \c string
  */
  abstract protected function getRoutesConfigFile();
  
  
  /*!
  ** Returns the path of the file which contains the configuration.
  **
  ** \return \c string
  */
  abstract protected function getConfigFile();
  
  
  /*!
  ** Constructor
  **
  ** \param [env]
  **          \c string - prod, dev, ….
  ** \param [debug]
  **          \c bool - Are we in debug mode.
  */
  public function __construct($env = 'prod', $debug = false)
  {
    $this->_env	  = $env;
    $this->_debug = $debug;
    
    if ($debug) {
      $this->start = microtime(true);
      init_set('display_error', 1);
      error_reporting(E_ALL | E_STRICT);
      set_error_handler(array('Ninaca\\Exceptions\\ErrorToException',
			      'error2Exception'));}
    else
      init_set('display_error', 0);
    $this->loadConfig();
    $this->checkApp();
  }
  
  
  /*!
  ** Run the application
  */
  public function run()
  {
  }
  
  
  /*!
  ** Return the actual environement.
  **
  ** \return \c string - dev, prod, …
  */
  public function getEnv()
  {
    return $this->_env;
  }
  
  
  /*!
  ** Return the number of seconds spent since the class has been instancied.
  **
  ** \param [precision]
  **          \c int - The number of decimal digits to round to.
  **
  ** \throw Ninaca\Exceptions\InvalidArgumenTypetException
  **     if \a precision is not an int.
  **
  ** \return \c int
  */
  public function getExecTime($precision = 4)
  {
    Debug::checkArg(0,
		    1, 'int', gettype($precision));
    
    return round(microtime(true) - $this->_start_time, $precision);
  }
  
  
  /*!
  ** Gets the config files.
  **
  ** \return \c string
  */
  protected function loadConfig()
  {
    $this->_configs['settings'] = new Config($this->getConfigFile());
    $this->_configs['routes'] = new Config($this->getRoutesConfigFile());
  }
}
