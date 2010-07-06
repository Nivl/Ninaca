<?php

/*!
**  \file	Ftp.php
**  \author	Nivl <nivl@free.fr>
**  \started	09/13/2009, 06:53 PM
**  \last	Nivl <nivl@free.fr> 06/02/2010, 05:36 PM
**  \copyright	Copyright (C) 2009 Laplanche Melvin
**  
**  Licensed under the MIT license:
**  <http://www.opensource.org/licenses/mit-license.php>.
**  For the full copyright and license information, please view the LICENSE
**  file that was distributed with this source code.
*/


namespace Ninaca\Utilities;
use \Ninaca\Utilities\Misc;
use \Ninaca\Exceptions\FtpException;
use \Ninaca\Exceptions\InvalidArgumentTypeException as Iate;


/*!
** Functions to manipulate directories and files.
*/
class Ftp
{
  /*!
  ** Gets the list of the files of one or several directories.
  **
  ** The two arguments takes by the closure cb_key are the filename and the
  **   filepath.
  **
  ** The two arguments takes by the closure cb_values are the filepath and the
  **  returns of cb_key.
  **
  ** \todo The wildcard * can’t be used for the exception list.
  **
  ** \param path
  **          \c string|array - Director(y/ies) to browse.
  ** \param list
  **          \c &array - Variable which will contains the list.
  ** \param [exts]
  **          \c array - Allowed extensions.
  ** \param [except]
  **          \c array - File or directory to not browse.
  ** \param [rec]
  **          \c bool - Browse recursively?
  ** \param [cb_key]
  **          \c \Closure - Callback function for the filename.
  ** \param [cb_value]
  **          \c \Closure - Callback Function for the filepath.
  ** \param [depth]
  **          \c int - Depth for debugage informations.
  ** \param [iate]
  **          \c int - Depth for InvalidArgumentTypeException.
  **
  ** \throw Ninaca\Exceptions\InvalidArgumentTypeException
  **     if \a iae is not an int or less than 0.
  ** \throw Ninaca\Exceptions\InvalidArgumentTypeException
  **     if \a depth is not an int or less than 0.
  ** \throw Ninaca\Exceptions\InvalidArgumentTypeException
  **     if \a path is not a string or is empty.
  */
  static public function getFilesFromDir($path,
					 array &$list,
					 array $exts = array(),
					 array $except = array(),
					 $rec = true,
					 \Closure $cb_key = null,
					 \Closure $cb_value = null,
					 $depth = 0,
					 $iate = 0)
  {
    Debug::checkArgs(0,
		     8, 'int', $depth,
		     8, array('greater than', -1), $depth,
		     9, 'int', $iate,
		     9, array('greater than', -1), $iate);
    Debug::checkArgs($iate,
		     1, 'string or array', $path,
		     1, 'nonempty', $path);
    
    $dirs = (array)$path;
    
    foreach ($dirs as $dir) {
      foreach (glob($path) as $file) {
	$filename = basename($file);
	if ( !in_array($file, $except) ) {
	  if ( $rec && is_dir($file) && !in_array($filename, array('.','..')) )
	    self::getFilesFromDir($file.'/*', $list, $exts, $except, $rec, 
				  $cb_key, $cb_value);
	  else if ( is_file($file) ) {
	    if ( self::checkFilesExtension($filename, $exts) ) {
	      $key   = ($cb_key) ? $cb_key($filename, $file) : $filename;
	      $value = ($cb_value) ? $cb_value($file,$key) : $file;
	      $list[$key] = $value; }}}}}
  }
  
  
  /*!
  ** Checks if a file has a valid extension.
  **
  ** \param filename
  **          \c string
  ** \param exts
  **          \c array
  ** \param [value_if_empty]
  **          \c bool - Value to return if \a exts is empty.
  ** \param [depth]
  **          \c int - Depth for debugage informations.
  ** \param [iate]
  **          \c int - Depth for InvalidArgumentTypeException.
  **
  ** \throw Ninaca\Exceptions\InvalidArgumentTypeException
  **     if \a iae is not an int or less than 0.
  ** \throw Ninaca\Exceptions\InvalidArgumentTypeException
  **     if \a depth is not an int or less than 0.
  ** \throw Ninaca\Exceptions\InvalidArgumentTypeException
  **     if \a filename is not a string.
  **
  ** \return \c bool
  */
  static public function checkFilesExtension($filename,
					     array $exts,
					     $value_if_empty = true,
					     $depth = 0,
					     $iate = 0)
  {
    Debug::checkArgs(0,
		     4, 'int', $depth,
		     4, array('greater than', -1), $depth,
		     5, 'int', $iate,
		     5, array('greater than', -1), $iate);
    Debug::checkArgs($iate,
		     1, 'string', $filename);
    
    if (Misc::isEmpty($exts))
      return (bool)$value_if_empty;
    foreach ( $exts as $ext ) {
      if (mb_substr($filename, -mb_strlen($ext)) === $ext)
	return true;}
    return false;
  }
  
  
  /*!
  ** Moves files and directories.
  **
  ** \param from
  **          \c string - Directory to browse/File to move.
  ** \param to
  **          \c string - Destination.
  ** \param [rec]
  **          \c bool - Browse recursively.
  ** \param [keep_dir]
  **          \c bool - Keep the same architecture.
  ** \param [options]
  **          \c array - Available options (for files only):
  **            \arg prefix     \c string - Prefix of the files.
  **            \arg suffix     \c string - Suffix (with extension).
  **            \arg extensions \c array  - Extensions of the files.
  ** \param [depth]
  **          \c int - Depth for debugage information.
  ** \param [iate]
  **          \c int - Depth for InvalidArgumentTypeException.
  **
  ** \throw Ninaca\Exceptions\InvalidArgumentTypeException
  **     if \a from is not a string or is empty.
  ** \throw Ninaca\Exceptions\InvalidArgumentTypeException
  **     if \a to is not a string or is empty.
  ** \throw Ninaca\Exceptions\InvalidArgumentTypeException
  **     if \a depth is not an int or less than 0.
  ** \throw Ninaca\Exceptions\InvalidArgumentTypeException
  **     if \a iae is not an int or less than 0.
  */
  static public function move($from,
			      $to,
			      $rec = true,
			      $keep_dir = true,
			      array $options = array(),
			      $depth = 0,
			      $iate = 0)
  {
    Debug::checkArgs(0,
		     6, 'int', $depth,
		     6, array('greater than', -1), $depth,
		     7, 'int', $iate,
		     7, array('greater than', -1), $iate);
    Debug::checkArgs($iate,
		     1, 'string', $from,
		     1, 'nonempty', $from,
		     2, 'string', $to,
		     2, 'nonempty', $to);
    
    if (is_file($from))
      self::moveFile($from, $to, $depth+1, $iate+1);
    else
      self::move_exec($from, $to, $rec, $keep_dir, $options, $depth+1);
  }
  
  
  /*!
  ** Moves files and directories.
  ** This function is a part of the move() function
  **
  ** \param from
  **          \c string - Directory to browse/File to move.
  ** \param to
  **          \c string - Destination.
  ** \param [rec]
  **          \c bool - Browse recursively.
  ** \param [keep_dir]
  **          \c bool - Keep the same architecture.
  ** \param [options]
  **          \c array - Available options (for files only):
  **            \arg prefix     \c string - Prefix of the files.
  **            \arg suffix     \c string - Suffix (with extension).
  **            \arg extensions \c array  - Extensions of the files.
  ** \param [depth]
  **          \c int - Depth for debugage information.
  */
  static private function move_exec($from,
				    $to,
				    $rec = true,
				    $keep_dir = true,
				    array $opts = array(),
				    $depth = 0)
  {
    $from = rtrim($from, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
    $to   = rtrim($to, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
    $exts = !empty($opts['extensions']) ? $opts['extensions'] : array();
    $pref = !empty($opts['prefix']) ? $opts['prefix'] : '';
    $suf  = !empty($opts['suffix']) ? $opts['suffix'] : '';
    $dir  = opendir($from);
    
    while (($file = readdir($dir)) !== false) {
      if ($rec && is_dir($from.$file) && !in_array($file, array('..','.'))) {
	$new_to = ($keep_dir) ? $to.$file : $to;
	self::makeDir($new_to);
	self::moveFiles($from.$file, $new_to, $rec, $keep_dir, $opts);}
      else if (is_file($from.$file) && self::checkFileExtension($file, $exts)){
	if ((!$pref || mb_substr($file, 0, mb_strlen($pref)-1) == $pref) &&
	    (!$suf || mb_substr($file, -mb_strlen($suf)) == $suf))
	  self::moveFile($from.$file, $to.$file);}}
    closedir($dir);
  }
  
  
  /*!
  ** Alias of move
  **
  ** \see move
  */
  static public function mv($from,
			    $to,
			    $rec = true,
			    $keep_dir = true,
			    array $options = array(),
			    $depth = 0,
			    $iate = 0)
  {
    self::move($from, $to, $rec, $keep_dir, $options, $depth+1, $iate+1);
  }
  
  
  /*!
  ** Move one file.
  **
  ** \param from
  **          \c string - Filename.
  ** \param to
  **          \c string - New filename or destination directory’s name.
  ** \param [depth]
  **          \c int - Depth for debugage information.
  ** \param [iate]
  **          \c int - Depth for InvalidArgumentTypeException.
  **
  ** \throw Ninaca\Exceptions\InvalidArgumentTypeException
  **     if \a from is not a string or is empty.
  ** \throw Ninaca\Exceptions\InvalidArgumentTypeException
  **     if \a to is not a string or is empty.
  ** \throw Ninaca\Exceptions\FtpException
  **     if the move fails.
  ** \throw Ninaca\Exceptions\InvalidArgumentTypeException
  **     if \a depth is not an int or less than 0.
  ** \throw Ninaca\Exceptions\InvalidArgumentTypeException
  **     if \a iae is not an int or less than 0.
  **
  ** \usage
  **       moveFile('/dir/to/file.jpg', /dir/dest/);
  **       moveFile('/dir/to/file.jpg', /dir/dest/img.jpg);
  ** \endusage
  */
  static public function moveFile($from,
				  $to,
				  $depth = 0,
				  $iate = 0)
  {
    Debug::checkArgs(0,
		     3, 'int', $depth,
		     4, 'int', $iate);
    Debug::checkArgs($iate,
		     1, 'string', $from,
		     1, 'nonempty', $from,
		     2, 'string', $to,
		     2, 'nonempty', $to);

    self::moveFile_exec($from, $to, $depth);
  }
  
  
  /*!
  ** Move one file.
  **
  ** \param from
  **          \c string - Filename.
  ** \param to
  **          \c string - New filename or destination directory’s name.
  ** \param [depth]
  **          \c int - Depth for debugage information.
  **
  ** \throw Ninaca\Exceptions\FtpException
  **     if the move fails.
  */
  static private function moveFile_exec($from,
					$to,
					$depth = 0)
  {
    if ($pos = mb_strrpos($to, DIRECTORY_SEPARATOR)) {
      $dest = mb_substr($to, 0, $pos);
      self::makeDir($dest);}
    if (!is_dir($to) && $pos !== false)
      $filename = '';
    else {
      if (is_dir($to) && mb_substr($to, -1) !== DIRECTORY_SEPARATOR)
	$to .= DIRECTORY_SEPARATOR;
      if ($pos = mb_strrpos($from, DIRECTORY_SEPARATOR))
	$filename = mb_substr($from, $pos+1);
      else
	$filename = $from;}
    try {
      copy($from, $to.$filename);
      unlink($from);}
    catch (\ErrorException $e) {
      throw new FtpException($e->getMessage(), $depth);}
  }
  
  
  /*!
  ** Removes file
  **
  ** If \a files is an array, each non-string values will be ignored.
  **
  ** \param files
  **          \c string|array
  ** \param [recursive] 
  **          \c string - Remove (sub-)directories and their contents.
  ** \param [depth]
  **          \c int - Depth for debugage information.
  ** \param [iate]
  **          \c int - Depth for InvalidArgumenTypetException.
  **
  ** \throw Ninaca\Exceptions\InvalidArgumentTypeException
  **     if \a file is not a string or is empty.
  ** \throw Ninaca\Exceptions\InvalidArgumentTypeException
  **     if \a depth is not an int or less to 0.
  ** \throw Ninaca\Exceptions\InvalidArgumentTypeException
  **     if \a iae is not an int or less to 0.
  ** \throw Ninaca\Exceptions\FtpException
  **     if \a filename is a directory, doesn’t exists, or isn’t writable.
  */
  static public function remove($files,
				$recursive = false,
				$depth = 0,
				$iate = 0)
  {
    Debug::checkArgs(0,
		     3, 'int', $depth,
		     4, 'int', $iate);
    Debug::checkArgs($iate,
		     1, 'string or array', $files,
		     1, 'nonempty', $files);

    self::remove_exec($file, $recursive, $depth);
  }
   

  /*!
  ** Removes file
  ** This method is a part of the remove() method.
  **
  ** If \a files is an array, each non-string values will be ignored.
  **
  ** \param files
  **          \c string|array
  ** \param [recursive] 
  **          \c string - Remove (sub-)directories and their contents.
  ** \param [depth]
  **          \c int - Depth for debugage information.
  **
  ** \throw Ninaca\Exceptions\FtpException
  **     if \a filename is a directory, doesn’t exists, or isn’t writable.
  */
  static public function remove_exec($files,
				     $recursive = false,
				     $depth = 0)
  {
    $files = (array)$files;
    foreach($files as $filename) {
      if (!is_string($filename))
	continue;
      if (is_file($filename) && is_writable($filename)) {
	return unlink($filename);}
      else if ($recursive && is_dir($filename) && is_writable($filename)) {
	$dirname = rtrim($filename, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
	$dir = opendir($dirname);
	while (($file = readdir($dir)) !== false) {
	  if (is_dir($dirname.$file) && !in_array($file, array('..','.'))) {
	    self::remove($dirname.$file, true);
	    rmdir($dirname);}
	  else if (is_file($dirname.$file))
	    unlink($dirname.$file);}
	closedir($dir);}
      else
	throw new FtpException("$filename is a directory, doesn’t exists, ".
			       'or is not writable', $depth);}
  }
  
  
  /*!
  ** Alias of remove
  **
  ** \see remove
  */
  static public function rm($files,
			    $recursive = false,
			    $depth = 0,
			    $iate = 0)
  {
    return self::remove($files, (bool)$recursive, $depth+1, $iate+1);
  }
  
  
  /*!
  ** Make one or more directories.
  **
  ** If \a dirs is an array each non-string values will be ignored.
  **
  ** \param dirs
  **          \c array|string
  ** \param [chmod]
  **          \c int
  ** \param [recursive]
  **          \c bool
  ** \param [umask]
  **          \c bool - Use umask to apply rights.
  ** \param [umask_value]
  **          \c bool - Value to give to umask.
  ** \param [depth]
  **          \c int - Depth for debugage information.
  ** \param [iate]
  **          \c int - Depth for InvalidArgumentTypeException.
  **
  ** \throw Ninaca\Exceptions\InvalidArgumentTypeException
  **     if \a depth is not an int or less to 0.
  ** \throw Ninaca\Exceptions\InvalidArgumentTypeException
  **     if \a iae is not an int or less to 0.
  ** \throw Ninaca\Exceptions\InvalidArgumentTypeException
  **     if \a dirs is not a string or array.
  ** \throw Ninaca\Exceptions\InvalidArgumentTypeException
  **     if \a dirs is empty.
  ** \throw Ninaca\Exceptions\InvalidArgumentTypeException
  **     if \a chmod is not an integer.
  ** \throw Ninaca\Exceptions\InvalidArgumentTypeException
  **     if \a umask_value is not an integer.
  ** \throw Ninaca\Exceptions\FtpException
  **        if the make fails.
  */
  static public function makeDir($dirs,
				 $chmod = 0755,
				 $recursive = true,
				 $umask = true,
				 $umask_value = 0,
				 $depth = 0,
				 $iate = 0)
  {
    Debug::checkArgs(0,
		     6, 'int', $depth,
		     7, 'int', $iate);
    Debug::checkArgs($iate,
		     1, 'string or array', $dirs,
		     1, 'nonempty', $dirs,
		     2, 'int', $chmod,
		     5, 'int', $umask_value);

    self::makeDir_exec($dirs,$chmod,$recursive,$umask,$umask_values,$depth);
  }


  /*!
  ** Make one or more directories.
  **
  ** If \a dirs is an array each non-string values will be ignored.
  **
  ** \param dirs
  **          \c array|string
  ** \param [chmod]
  **          \c int
  ** \param [recursive]
  **          \c bool
  ** \param [umask]
  **          \c bool - Use umask to apply rights.
  ** \param [umask_value]
  **          \c bool - Value to give to umask.
  ** \param [depth]
  **          \c int - Depth for debugage information.
  **
  ** \throw Ninaca\Exceptions\FtpException
  **        if the make fails.
  */
  static private function makeDir_exec($dirs,
				       $chmod = 0755,
				       $recursive = true,
				       $umask = true,
				       $umask_value = 0,
				       $depth = 0)
  {
    $dirs = (array)$dirs;
    try {
      foreach ($dirs as $dir) {
	if (!is_string($dir) || is_dir($dir))
	  continue;
	if ($umask) {
	  $um = umask($umask_value);
	  mkdir($dir, $chmod, (bool)$recursive);
	  umask($um);}
	else
	  mkdir($dir, $chmod, (bool)$recursive);}}
    catch (\ErrorException $e) {
      throw new FtpException($e->getMessage(), $depth);}
  }


  /*!
  ** Alias of makeDir
  **
  ** \see makeDir
  */
  static public function mkdir($dirs,
			       $chmod = 0755,
			       $recursive = true,
			       $umask = true,
			       $umask_value = 0,
			       $depth = 0,
			       $iate = 0)
  {
    return $this->makeDir($dirs, $chmod, $recursive, $umask, $umask_value,
			  $depth+1, $iate+1);
  }


  /*!
  ** Returns the size of a file.
  **
  ** \param file
  **          \c string
  ** \param [depth]
  **          \c int - Depth for debugage information.
  ** \param [iate]
  **          \c int - Depth for InvalidArgumentTypeException.
  **
  ** \throw Ninaca\Exceptions\FtpException
  **     if \a file isn’t readable or doesn’t exists.
  ** \throw Ninaca\Exceptions\InvalidArgumentTypeException
  **     if \a file is not a string or is empty.
  ** \throw Ninaca\Exceptions\InvalidArgumentTypeException
  **     if \a depth is not an int or less to 0.
  ** \throw Ninaca\Exceptions\InvalidArgumentTypeException
  **     if \a iae is not an int or less to 0.
  **
  ** \return \c string
  */
  static public function getFileSize($file,
				     $depth = 0,
				     $iate = 0)
  {
    Debug::checkArgs(0,
		     2, 'int', $depth,
		     3, 'int', $iate);
    Debug::checkArgs($iate,
		     1, 'string', $file,
		     1, 'nonempty', $file);

    if (!is_file($file))
      throw new FtpException("$file is not readable.", $depth);
    return sprintf('%u', filesize($file));
  }
  
  
  /*!
  ** Convert the value of \a $size in \a $to
  ** The textual suffixes are accepted. Ex: 1M, 5k, 10G
  **
  ** \param size
  **          \c int|float|string
  ** \param [to]
  **          \c char
  ** \param [si]
  **          \c bool - 1M = 1000o if true, else 1M = 1024.
  ** \param [depth]
  **          \c int - Depth for debugage information.
  ** \param [iate]
  **          \c int - Depth for InvalidArgumentTypeException.
  **
  ** \throw Ninaca\Exceptions\InvalidArgumentTypeException
  **     if \a size is not valid.
  ** \throw Ninaca\Exceptions\InvalidArgumentTypeException
  **     if \a to is not a valid char.
  ** \throw Ninaca\Exceptions\InvalidArgumentTypeException
  **     if \a depth is not an int or less to 0.
  ** \throw Ninaca\Exceptions\InvalidArgumentTypeException
  **     if \a iae is not an int or less to 0.
  **
  ** \return \c int|float
  */
  static public function octetConverter($size,
					$to = 'o',
					$si = false,
					$depth = 0,
					$iate = 0)
  {
    Debug::checkArgs(0,
		     4, 'int', $depth,
		     5, 'int', $iate);
    Debug::checkArgs($iate,
		     2, 'char', $to);

    if (!preg_match('`^-?[0-9]+\.?[0-9]*[okmgt]?$`i', $size))
      throw new Iate(1, 'a number which can be followed '.
		        'by a valid char', $size, $iate);
    $to = strtolower($to);
    if (!in_array($to, array('o', 'k', 'm', 'g', 't')))
      throw new Iate(2, 'an existing unit', $to, $iate);
    
    self::octetConverter_exec($size, $to, $si);
  }
  
  
  /*!
  ** Convert the value of \a $size in \a $to
  ** The textual suffixes are accepted. Ex: 1M, 5k, 10G
  **
  ** \param size
  **          \c int|float|string
  ** \param [to]
  **          \c char
  ** \param [si]
  **          \c bool - 1M = 1000o if true, else 1M = 1024.
  **
  ** \return \c int|float
  */
  static private function octetConverter_exec($size,
					      $to = 'o',
					      $si = false)
  {
    $unit = is_numeric($size) ? 'o' : strtolower($size[strlen($size)-1]);
    $base = ($si) ? 10 : 2;
    if ($si)
      $pow = array('o'=>1, 'k'=>3, 'm'=>6, 'g'=>9, 't'=>12);
    else
      $pow = array('o'=>1, 'k'=>10, 'm'=>20, 'g'=>30, 't'=>40);
    if ($to === $unit)
      return $size * 1;
    else if (Arrays::getKey($to, $units) < Arrays::getKey($unit, $units))
      return $size * pow($base, $pow[$unit]);
    else
      return $size / pow($base, $pow[$to]);
  }
  
  
  /*!
  ** Returns the mimetype of a file.
  **
  ** \param file
  **          \c string
  ** \param [depth]
  **          \c int - Depth for debugage information.
  ** \param [iate]
  **          \c int - Depth for InvalidArgumentTypeException.
  **
  ** \throw Ninaca\Exceptions\FtpException
  **     if \a file isn’t readable or doesn’t exists.
  ** \throw Ninaca\Exceptions\InvalidArgumentTypeException
  **     if \a file is not a string or is empty.
  ** \throw Ninaca\Exceptions\InvalidArgumentTypeException
  **     if \a depth is not an int or less to 0.
  ** \throw Ninaca\Exceptions\InvalidArgumentTypeException
  **     if \a iae is not an int or less to 0.
  ** 
  ** \return \c string
  */
  static public function getMimeType($file,
				     $depth = 0,
				     $iate = 0)
  {
    Debug::checkArgs(0,
		     2, 'int', $depth,
		     3, 'int', $iate);
    Debug::checkArgs($iate,
		     1, 'string', $file,
		     1, 'nonempty', $file);
    
    if (!is_file($file) || !is_readable($file))
      throw new FtpException("$file is not readable, or doesn’t exists.");
    if (class_exists('finfo') && is_int(FILEINFO_MIME_TYPE)) {
      $Finfo = new \finfo(FILEINFO_MIME_TYPE);
      return $Finfo->file($file);}
    else
      return mime_content_type($file);
  }
  
  
  /*!
  ** Returns the new dimensions of an image to rescale.
  **
  ** \param w
  **          \c int - Current width (>= 1).
  ** \param h
  **          \c int - Current height (>= 1).
  ** \param [max_h]
  **          \c int - Max width ( >= 10).
  ** \param [max_w]
  **          \c int - Max width ( >= 10).
  ** \param [depth]
  **          \c int - Depth for debugage information.
  ** \param [iate]
  **          \c int - Depth for InvalidArgumenTypetException.
  **
  ** \throw Ninaca\Exceptions\InvalidArgumentTypeException
  **     if \a w is not an int greater than 0.
  ** \throw Ninaca\Exceptions\InvalidArgumentTypeException
  **     if \a h is not an int greater than 0.
  ** \throw Ninaca\Exceptions\InvalidArgumentTypeException
  **     if \a max_w is not an int greater than 9.
  ** \throw Ninaca\Exceptions\InvalidArgumentTypeException
  **     if \a max_h is not an int greater than 9.
  ** \throw Ninaca\Exceptions\InvalidArgumentTypeException
  **     if \a depth is not an int or less than 0.
  ** \throw Ninaca\Exceptions\InvalidArgumentTypeException
  **     if \a iae is not an int or less than 0.
  **
  ** \return \c array
  **         \arg \e width \c int
  **         \arg \e heigth \c int
  */
  static public function rescaleImage($w,
				      $h,
				      $max_w = 200,
				      $max_h = 200,
				      $depth = 0,
				      $iate = 0)
  {
    Debug::checkArgs(0,
		     5, 'int', $depth,
		     6, 'int', $iate);
    Debug::checkArgs($iate,
		     1, 'int', $w,
		     1, array('greater than', 0), $w,
		     2, 'int', $h,
		     2, array('greater than', 0), $h,
		     3, 'int', $max_w,
		     3, array('greater than', 10), $max_w,
		     4, 'int', $max_h,
		     4, array('greater than', 10), $max_h);
    
    return self::rescaleImage_exec($w, $h, $max_w, $max_h);
  }
  
  
  /*!
  ** Returns the new dimensions of an image to rescale.
  ** This function is a part of the resacleImage() function.
  **
  ** \param w
  **          \c int - Current width.
  ** \param h
  **          \c int - Current height.
  ** \param max_h
  **          \c int - Max width.
  ** \param max_w
  **          \c int - Max width.
  **
  ** \return \c array
  **         \arg \e width \c int
  **         \arg \e heigth \c int
  */
  static private function rescaleImage_exec($w, $h, $max_w, $max_h)
  {
    if ($w > $max_w || $h > $max_h) {
      if (($w / $max_w) < ($h / $max_h)) {
	$new_h = $max_h;
	$red = ($new_h * 100) / $h;
	$new_w = ($w * $red) / 100;}
      else {
	$new_w = $max_w;
	$red = ($new_w * 100) / $w;
	$new_h = ($h * $red) / 100; }}
    else {
      $new_w = $w;
      $new_h = $h;}
    return array('width' => $new_w, 'height' => $new_h);
  }
}