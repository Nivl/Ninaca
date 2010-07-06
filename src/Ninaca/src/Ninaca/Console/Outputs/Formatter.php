<?php

/*!
**  \file	Formatter.php
**  \author	Nivl <nivl@free.fr>
**  \started	05/23/2010, 10:59 PM
**  \last	Nivl <nivl@free.fr> 05/24/2010, 09:58 PM
**  \copyright	Copyright (C) 2009 Laplanche Melvin
**  
**  Licensed under the MIT license:
**  <http://www.opensource.org/licenses/mit-license.php>.
**  For the full copyright and license information, please view the LICENSE
**  file that was distributed with this source code.
*/


namespace Ninaca\Console\Outputs;
use \Ninaca\Utilities\Debug;
use \Ninaca\Exceptions\InvalidArgumentTypeException as Iate;

/*!
** Formats outputs.
*/
class Formatter
{
  protected
    $_fgColors = array(),
    $_bgColors = array(),
    $_options  = array(),
    $_current  = array();
  
  
  /*!
  ** Constructor
  **
  ** \param [fg]
  **          \c string - Color for the foreground.
  ** \param [bg]
  **          \c string - Color for the background.
  ** \param [opt]
  **          \c string - Option for the text.
  */
  public function __construct($fg = 'white',
			      $bg = 'black',
			      $opt = 'conceal')
  {
    Debug::checkArgs(0,
		     1, 'string', $fg,
		     2, 'string', $bg,
		     3, 'string', $opt);
    
    $this->_defineForegroundColors();
    $this->_defineBackgroundColors();
    $this->_defineOptions();
    $this->setForegroundColor($fg);
    $this->setBackgroundColor($bg);
    $this->setOption($opt);
  }
  
  
  /*!
  ** Sets the foreground color.
  **
  ** \param [fg]
  **          \c string - Color of the foreground.
  */
  public function setForegroundColor($fg = 'white')
  {
    Debug::checkArgs(0,
		     1, 'string', $fg);
    
    if (!isset($this->_fgColors[$fg]))
      throw new Iate(1, 'an existing color', $fg);
    $this->_current['fg_color'] = $fg;
  }
  
  
  /*!
  ** Sets the background color.
  **
  ** \param [bg]
  **          \c string - Color of the background.
  */
  public function setBackgroundColor($bg = 'black')
  {
    Debug::checkArgs(0,
		     1, 'string', $bg);
    
    if (!isset($this->_bgColors[$bg]))
      throw new Iate(1, 'an existing color', $bg);
    $this->_current['bg_color'] = $bg;
  }
  
  
  /*!
  ** Sets an option for the string.
  **
  ** \param [opt]
  **          \c string
  */
  public function setOption($opt = 'conceal')
  {
    Debug::checkArgs(0,
		     1, 'string', $opt);
    
    if (!isset($this->_options[$opt]))
      throw new Iate(1, 'an existing option', $opt);
    $this->_current['option'] = $opt;
  }
  
  
  /*!
  ** Formats a string
  **
  ** \param str
  **          \c string - string to format.
  */
  public function format($str)
  {
    $fg  = $this->_fgColors[$this->_current['fg_color']];
    $bg  = $this->_bgColors[$this->_current['bg_color']];
    $opt = $this->_options[$this->_current['option']];
    
    return "\033[{$fg}m"."\033[{$bg}m"."\033[{$opt}m".$str."\033[0m";
  }
  
  
  /*!
  ** Define the foreground colors
  */
  protected function _defineForegroundColors()
  {
    $this->_fgColors = array('default' => 0,
                             'black'   => 30,
			     'red'     => 31,
			     'green'   => 32,
			     'yellow'  => 33,
			     'blue'    => 34,
			     'magenta' => 35,
			     'cyan'    => 36,
			     'white'   => 37);
  }
  
  
  /*!
  ** Define the background colors
  */
  protected function _defineBackgroundColors()
  {
    $this->_bgColors = array('default' => 0,
                             'black'   => 40,
			     'red'     => 41,
			     'green'   => 42,
			     'yellow'  => 43,
			     'blue'    => 44,
			     'magenta' => 45,
			     'cyan'    => 46,
			     'white'   => 47);
  }


  /*!
  ** Define the text options.
  */
  protected function _defineOptions()
  {
    $this->_options = array('default'    => 0,
			    'bold'       => 1,
			    'underscore' => 4,
			    'blink'      => 5,
			    'reverse'    => 7,
			    'conceal'    => 8);
  }
}

