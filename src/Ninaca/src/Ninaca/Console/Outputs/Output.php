<?php

/*!
**  \file	Output.php
**  \author	Nivl <nivl@free.fr>
**  \started	05/16/2010, 04:06 PM
**  \last	Nivl <nivl@free.fr> 05/24/2010, 09:22 PM
**  \copyright	Copyright (C) 2009 Laplanche Melvin
**  
**  Licensed under the MIT license:
**  <http://www.opensource.org/licenses/mit-license.php>.
**  For the full copyright and license information, please view the LICENSE
**  file that was distributed with this source code.
*/


namespace Ninaca\Console\Outputs;


/*!
** Main class for outputs.
*/
abstract class Output
{
  /*!
  ** Writes a text.
  **
  ** \param mess
  **          \c string - Message to write.
  ** \param Formatter
  **          \c Formatter
  */
  abstract public function write($mess, Formatter $Formatter = null);
  
  
  /*!
  ** Writes a text on a new line.
  **
  ** \param mess
  **          \c string - Message to write.
  ** \param Formatter
  **          \c Formatter
  */
  abstract public function writeLine($mess, Formatter $Formatter);
  
  
  /*!
  ** Writes a message box.
  **
  ** \param mess
  **          \c string - Message to write.
  ** \param Formatter
  **          \c Formatter
  */
  abstract public function writeBox($mess, Formatter $format);
  
  
  /*!
  ** Write a list (key : value) 
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
  abstract public function writeList(array $list,
				     Formatter $F_okey, Formatter $F_ovalue,
				     Formatter $F_ekey, Formatter $F_evalue);
}

