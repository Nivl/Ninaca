<?php

/*!
**  \file	Debug.php
**  \author	Nivl <nivl@free.fr>
**  \started	04/16/2010, 04:31 PM
**  \last	Nivl <nivl@free.fr> 05/18/2010, 04:37 PM
**  \copyright	Copyright (C) 2009 Laplanche Melvin
**  
**  Licensed under the MIT license:
**  <http://www.opensource.org/licenses/mit-license.php>.
**  For the full copyright and license information, please view the LICENSE
**  file that was distributed with this source code.
*/


namespace Ninaca\Utilities;
use Ninaca\Exceptions\InvalidArgumentTypeException as Iate;


/*!
** This class contains usefull methods for debuging projects.
*/
class Debug
{
  /*!
  ** This function returns some informations about where the
  ** function which called this one has been called.
  **
  ** \param [depth]
  **          \c int - Depth where we start to search.
  ** \param [count] 
  **          \c int - Number of entries to browse before stopping.
  ** \param [real]
  **          \c bool - Use the real depth.
  **
  ** \throw Ninaca\Exceptions\InvalidArgumentTypeException
  **     if \a depth is not a int >= 0.
  ** \throw Ninaca\Exceptions\InvalidArgumentTypeException
  **     if \a count is not a int >= 0.
  **
  ** \return \c array
  **         \arg \e file \c string
  **         \arg \e class \c string
  **         \arg \e function \c string
  **         \arg \e line \c string
  */
  static public function whoCalledMe($depth = 0,
				     $count = 0,
				     $real = false)
  {
    if (!is_int($depth) && !ctype_digit($depth))
      throw new Iate(1, 'int (greater or equal to 0)', gettype($depth));
    else if ($depth < 0)
      throw new Iate(1, 'greater or equal to 0', $depth);
    if (!is_int($count) && !ctype_digit($count))
      throw new Iate(2, 'int (greater or equal to 0)', gettype($count));
    else if ($count < 0)
      throw new Iate(2, 'greater or equal to 0', $count);

    $depth += $real ? 0 : 3;
    $entry = self::whoCalledMe_getEntry($depth);
    $it_was_me = self::whoCalledMe_getInfo($entry);
    if (!empty($it_was_me))
      return $it_was_me;
    else
      return $count > 0 ? self::whoCalledMe($depth+2, --$count,true) : array();
  }
  
  
  /*!
  ** Returns the needed entry from debug_print_backtrace().
  ** This method is a part of the whoCalledMe method.
  **
  ** \param depth
  **          \c int - Depth where we start to search.
  **
  ** \return \c string
  */
  static private function whoCalledMe_getEntry($depth)
  {
    ob_start();
    debug_print_backtrace();
    $infos = ob_get_contents();
    ob_end_clean();
    $id = '#'.++$depth;
    if (($entry_pos = mb_strstr($infos, $id)) === false)
      $entry_pos = mb_strstr($infos, '#0');
    $entry = str_replace($entry_pos, "", $infos);
    return mb_strrchr($entry ?: $infos, '#');
  }
  
  
  /*!
  ** Returns the filename and the line of an entry.
  ** This method is a part of the whoCalledMe method.
  **
  ** \param entry
  **          \c string - An entry from debug_print_backtrace().
  **
  ** \return \c array
  **         \arg \e file \c string
  **         \arg \e class \c string
  **         \arg \e function \c string
  **         \arg \e line \c string
  */
  static private function whoCalledMe_getInfo($entry)
  {
    list($class, $function) = self::whoCalledMe_getClassFunc($entry);
    list($file, $line) = self::whoCalledMe_getFileLine($entry);
    return array('file' => $file, 'class' => $class,
		 'function' => $function, 'line' => $line);
  }

  
  /*!
  ** Returns the name of the class and the function of an entry.
  ** This method is a part of the whoCalledMe method.
  **
  ** \param entry
  **          \c string - An entry from debug_print_backtrace().
  **
  ** \return \c array
  **         \arg \e 0 \c string - Name of the class.
  **         \arg \e 1 \c string - Name of the function.
  */
  static private function whoCalledMe_getClassFunc($entry)
  {
    if (!empty($entry)) {
      $entry = mb_substr($entry, 4);
      if (($function = mb_strstr($entry, '(', true)) === false)
	$function = $entry;
      if (mb_strpos($function, '::'))
	$infos = explode('::', $function);
      elseif (mb_strpos($function, '->'))
	$infos = explode('->', $function);
      else
	$infos = array('', $function);
      return array($infos[0], $infos[1]);}
    return array('','');
  }


  /*!
  ** Returns the filename and the line of an entry.
  ** This method is a part of the whoCalledMe method.
  **
  ** \param entry
  **          \c string - An entry from debug_print_backtrace().
  **
  ** \return \c array
  **         \arg \e 0 \c string - Name of the file.
  **         \arg \e 1 \c string - Number of the line.
  */
  static private function whoCalledMe_getFileLine($entry)
  {
    if (($file_line = mb_strrchr($entry, '/')) === false)
      return array();
    if (mb_strpos($file_line, "eval()'d")){
      $pos = mb_strpos($file_line, ')');
      $pos2 = mb_strrpos($file_line, ':') + 1;
      $useless_part = mb_substr($file_line, $pos, $pos2-$pos);
      $file_line = str_replace($useless_part, '/', $file_line);
      $file_line = str_replace('(', ':', $file_line);}
    return explode(':',mb_substr($file_line, 1, -2));
  }


  /*!
  ** Check if the arguments given to a function are valid.
  **
  ** The valid types are:
  ** <ul>
  **  <li>Strings 
  **   <ul>
  **    <li>int</li>
  **    <li>integer</li>
  **    <li>string</li>
  **    <li>ressource</li>
  **    <li>char</li>
  **    <li>nonempty</li>
  **    <li>string or array</li>
  **    <li>array or string</li>
  **   </ul>
  **  </li>
  **
  **  <li>Arrays
  **   <ul>
  **    <li>greater than</li>
  **    <li>less than</li>
  **    <li>equal to</li>
  **   </ul>
  **  </li>
  ** </ul>
  **
  ** \param depth
  **          \c int - Depth for debugage information.
  ** \param iate
  **          \c int - Depth for InvalidArgumentTypeException.
  ** \param [arg_num]
  **          \c int - Number of the argument.
  ** \param [type]
  **          \c string|array - Expected type.
  ** \param [arg]
  **          \c mixed - Value given.
  ** \param [arg_num]
  ** \param [type]
  ** \param [arg]
  ** \param […]
  **
  ** \throw Ninaca\Exceptions\InvalidArgumentTypeException
  **     if the number of a parameter isn’t an int.
  ** \throw Ninaca\Exceptions\InvalidArgumentTypeException
  **     if the type of a parameter isn’t an array or string.
  ** \throw Ninaca\Exceptions\InvalidArgumentTypeException
  **     if a test fails.
  **
  ** \usage
  **         checkArg(0,
  **                  1, 'string', $filename,
  **                  1, 'nonempty', $filename,
  **                  2, 'int', $age,
  **                  2, array('greater than', 17), $age);
  ** \endusage
  */
  static public function checkArgs($iate)
  {
    $iate     += 1;
    $args    = func_get_args();
    $nb_args = count($args);
    
    for ($i=1; $i<$nb_args; ++$i) {
      $arg = $args[$i];
      if ($i % 3 === 2) {
	if (!is_string($arg) && !is_array($arg))
	  throw new Iate($i, 'string or array', gettype($arg));
	$type = $arg;}
      else if ($i % 3 === 1) {
	if (!Misc::isInt($arg))
	  throw new Iate($i, 'int', gettype($arg));
	$num = $arg;}
      else
	self::checkArgs_check($num, $type, $arg, $iate);}
  }
  
  
  /*!
  ** Checks if the arguments are valid.
  ** This method is a part of the checkArg method.
  **
  ** \param num
  **          \c int - Position of the argument.
  ** \param type
  **          \c string|array - Expected type.
  ** \param arg
  **          \c mixed - Value of the argument.
  ** \param iate
  **          \c int - Depth for InvalidArgumentTypeException.
  */
  static private function checkArgs_check($num,
					  $type,
					  $arg,
					  $iate)
  {
    $iate   += 1;
    $types = array('int','integer','string','char','nonempty','ressource',
		   'string or array', 'array or string');
    if (!is_array($type) && !in_array($type, $types))
	throw new Iate($i, 'an existing type', $type);

    if (($type === 'int' || $type === 'integer') && !Misc::isInt($arg))
      throw new Iate($num, 'int', gettype($arg), $iate);
    else if ($type === 'string' && !is_string($arg))
      throw new Iate($num, 'string', gettype($arg), $iate);
    else if ($type === 'char' && !is_string($arg) && mb_strlen($arg) > 1)
      throw new Iate($num, 'char', gettype($arg), $iate);
    else if ($type === 'nonempty' && Misc::isEmpty($arg))
      throw new Iate($num, 'nonempty', 'empty value', $iate);
    else if ($type === 'ressource' && !$arg)
      throw new Iate($num, 'ressource', gettype($arg), $iate);
    else if (($type === 'string or array' || $type === 'array or string')
	     && !is_string($arg) && !is_array($arg))
      throw new Iate($num, 'string or array', gettype($arg), $iate);
    else if (is_array($type) && count($type) == 2 && isset($type[0],$type[1]))
      self::checkArgs_checkArray($num, $type, $arg, $iate);
  }


  
  
  
  /*!
  ** Checks if the arguments are valid.
  ** This method is a part of the checkArg method.
  **
  ** \param num
  **          \c int - Position of the argument.
  ** \param type
  **          \c array - Expected type.
  ** \param arg
  **          \c mixed - Value of the argument.
  ** \param iate
  **          \c int - Depth for InvalidArgumenTypetException.
  */
  static private function checkArgs_checkArray($num,
					       array $type,
					       $arg,
					       $iate)
  {
    $iate += 1;
    $types = array('greater than', 'less than', 'equal to');
    if (!in_array($type[0], $types))
	throw new Iate($i, 'an existing type', $type[0], 1);
    
    if ($type[0] === 'greater than' && Misc::isInt($type[1])) {
      if ($arg <= $type[1])
	throw new Iate($num, "greater than {$type[1]}", $arg, $iate);}
    else if ($type[0] === 'less than' && Misc::isInt($type[1])) {
      if ($arg >= $type[1])
	throw new Iate($num, "less than {$type[1]}", $arg, $iate);}
    else if ($type[0] === 'equal to' && $type[1] != $arg)
      throw new Iate($num, "equal to {$type[1]}", $arg, $iate);
  }
}


