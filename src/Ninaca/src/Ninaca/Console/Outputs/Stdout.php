<?php

/*!
**  \file	Stdout.php
**  \author	Nivl <nivl@free.fr>
**  \started	05/24/2010, 04:38 PM
**  \last	Nivl <nivl@free.fr> 06/27/2010, 07:13 PM
**  \copyright	Copyright (C) 2009 Laplanche Melvin
**  
**  Licensed under the MIT license:
**  <http://www.opensource.org/licenses/mit-license.php>.
**  For the full copyright and license information, please view the LICENSE
**  file that was distributed with this source code.
*/


namespace Ninaca\Console\Outputs;
use \Ninaca\Utilities\Arrays;
use \Ninaca\Utilities\Debug;


/*!
** Use stdout to display messages.
*/
class Stdout extends Output
{
  protected
    $_output = null;
  
  private
    $___last = null;
  
  
  public function __construct()
  {
    $this->_output = fopen('php://stdout', 'w');
  }


  public function __destruct()
  {
    fclose($this->_output);
  }

  
  /*!
  ** Writes a text.
  **
  ** \param mess
  **          \c string - Message to write.
  ** \param Formatter
  **          \c Formatter
  */
  public function write($mess,
                        Formatter $Formatter = null)
  {
    Debug::checkArgs(0,
		     1, 'string', $mess);

    $mess = $Formatter <> null ? $Formatter->format($mess) : $mess;
    fwrite($this->_output, $mess);
    $last = 'write';
  }


  /*!
  ** Writes a text on a new line.
  **
  ** \param mess
  **          \c string - Message to write.
  ** \param Formatter
  **          \c Formatter
  */
  public function writeLine($mess,
                            Formatter $Formatter)
  {
    Debug::checkArgs(0,
		     1, 'string', $mess);

    $mess = $Formatter <> null ? $Formatter->format($mess) : $mess;
    fwrite($this->_output, PHP_EOL.$mess);
    $last = 'writeLine';
  }


  /*!
  ** Writes a message box.
  **
  ** \param mess
  **          \c string - Message to write.
  ** \param Formatter
  **          \c Formatter
  */
  public function writeBox($mess,
                           Formatter $Formatter)
  {
    Debug::checkArgs(0,
		     1, 'string', $mess);
    
    $mess = str_repeat(' ', 80).PHP_EOL.
      $this->wrap80($mess, ' ', ' ', true, ' ').PHP_EOL.
      str_repeat(' ', 80).PHP_EOL;
    
    $mess   = $Formatter <> null ? $Formatter->format($mess) : $mess;
    $mess   = PHP_EOL.$mess.PHP_EOL;
    /*$mess = $this->___last <> 'writeBox' ? PHP_EOL.$mess : $mess;*/
    fwrite($this->_output, $mess);
    $this->___last = 'writeBox';
  }
  
  
  /*!
  ** Writes a list (key : value) 
  **
  ** \param list
  **          \c array
  **
  ** \param F_okey
  **          \c Formatter - Formatter for the key of the odd line.
  ** \param F_ovalue
  **          \c Formatter - Formatter for the value of the odd line.
  ** \param F_ekey
  **          \c Formatter - Formatter for the key of the even line.
  ** \param F_evalue
  **          \c Formatter - Formatter for the value of the even line.
  */
  public function writeList(array $list,
                            Formatter $F_okey = null,
			    Formatter $F_ovalue = null,
                            Formatter $F_ekey = null,
			    Formatter $F_evalue = null)
  {
    $i	      = 1;
    $out      = null;
    $list     = Arrays::padKeys($list);
    $F_okey   = $F_okey === null ? new Formatter() : $F_okey;
    $F_ovalue = $F_ovalue === null ? $F_okey : $F_ovalue;
    $F_ekey   = $F_ekey === null ? new Formatter() : $F_ekey;
    $F_evalue = $F_evalue === null ? $F_ekey : $F_evalue;
    
    foreach ($list as $key => $value) {
      $out .= PHP_EOL.' ';
      if ($i % 2) 
	$out .= $F_okey->format($key.':').$F_ovalue->format(' '.$value);
      else
	$out .= $F_ekey->format($key.':').$F_evalue->format(' '.$value);
      ++$i;}
    reset($list);
    $nb_nl_space = mb_strlen(key($array)) + 1;
    fwrite($this->_output, $this->_wrap80($out, str_repeat(' ',$nb_nl_space)));
  }
  
  
  /*!
  ** Wraps a string to 80 characters.
  **
  ** \param string
  **          \c string
  ** \param [nl]
  **          \c string - String to put on the begining of the new lines.
  ** \param [el]
  **          \c string - String to put on the end of the new lines
  ** \param [fill80]
  **          \c bool - All line of the string must have 80 chars.
  ** \param [char]
  **          \c char - Char used to fill the line.
  **
  ** \return \c string
  **
  ** \bug extra_len must be < than 80.
  ** \bug The value of \a char is not checked, it must be a char.
  */
  protected function wrap80($string,
			    $nl = '',
			    $el = '',
			    $fill80 = false,
			    $char = ' ')
  {
    $extra_len = mb_strlen($nl.$el);
    $strings = explode(PHP_EOL, $string);
    foreach ($strings as $key => $str) {
      $str = wordwrap($str, 80-$extra_len, PHP_EOL);
      if ($fill80) {
	$substrs = explode(PHP_EOL, $str);
	foreach ($substrs as &$substr) {
	  $nb = (80 - (mb_strlen($substr)+$extra_len)) / 2;
	  $chars = str_repeat($char, $nb);
	  $substr = $nl.$chars.$substr.$chars;
	  if (is_float($nb))
	    $substr .= $char;
	  $substr .= $el;}
	$strings[$key] = implode(PHP_EOL, $substrs);}
      else
	$strings[$key] = str_replace(PHP_EOL, $ol.PHP_EOL.$nl, $str);}
    return implode(PHP_EOL, $strings);
  }
}


