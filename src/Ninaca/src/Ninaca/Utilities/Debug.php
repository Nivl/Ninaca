<?php

/*!
**  \file	Debug.php
**  \author	Nivl <nivl@free.fr>
**  \started	04/16/2010, 04:31 PM
**  \last	Nivl <nivl@free.fr> 04/19/2010, 03:03 AM
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


namespace Ninaca\Utilities;
use Ninaca\Exceptions\InvalidArgumentException;

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
  ** \throw Ninaca\Exceptions\InvalidArgumentException
  **     if \a depth is not a int >= 0.
  ** \throw Ninaca\Exceptions\InvalidArgumentException
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
      throw new InvalidArgumentException(1, 'int (greater or equal to 0)',
					 gettype($depth));
    else if ($depth < 0)
      throw new InvalidArgumentException(1, 'greater or equal to 0', $depth);
    if (!is_int($count) && !ctype_digit($count))
      throw new InvalidArgumentException(2, 'int (greater or equal to 0)',
					 gettype($count));
    else if ($count < 0)
      throw new InvalidArgumentException(2, 'greater or equal to 0', $count);

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
    if (mb_strpos($file_line, 'eval')){
      $pos = mb_strpos($file_line, ')');
      $pos2 = mb_strrpos($file_line, ':') + 1;
      $useless_part = mb_substr($file_line, $pos, $pos2-$pos);
      $file_line = str_replace($useless_part, '/', $file_line);
      $file_line = str_replace('(', ':', $file_line);}
    return explode(':',mb_substr($file_line, 1, -2));
  }
}