<?php

/*!
**  \file	File.php
**  \author	Nivl <nivl@free.fr>
**  \started	12/20/08
**  \last	Nivl <nivl@free.fr> 05/15/2010, 11:34 PM
**  \copyright	Copyright (C) 2008 Laplanche Melvin
**  
**  Licensed under the MIT license:
**  <http://www.opensource.org/licenses/mit-license.php>.
**  For the full copyright and license information, please view the LICENSE
**  file that was distributed with this source code.
*/


namespace Ninaca\Caches;
use Ninaca\Exceptions\FtpException;
use Ninaca\Exceptions\InvalidArgumentException;
use Ninaca\Uttilities\Ftp;
use Ninaca\Uttilities\Misc;

/*!
**  This class uses Ftp to store data.
*/
class File implements Cache
{
  private
    $__time = 0,
    $__root = '';
  
  
  /*!
  ** Constructor
  */
  public function __construct()
  {
    $this->__time = microtime(true);
  }


  


  /*!
  ** Return the class’ id
  **
  ** \return \c string
  */
  public function getId()
  {
    return 'file';
  }
  
  
  /*!
  ** set the root directory.
  **
  ** \param dir
  **          \c string
  **
  ** \throw Ninaca\Exceptions\InvalidArgumentException
  **     if \a dir isn’t a string or is empty.
  ** \throw Ninaca\Exceptions\FtpException
  **     if \a dir is not writable or doens’t exisis.
  */
  public function setRootDir($dir)
  {
    if (!is_string($dir))
      throw new InvalidArgumentException(1, 'string', gettype($dir));
    if ($dir === '')
      throw new InvalidArgumentException(1, 'nonempty string', 'empty string');
    
    if (!is_dir($dir) || !is_writable($dir))
      throw new FtpException("The directory $dir is not writable.");
    $ds = DIRECTORY_SEPARATOR;
    $this->__root = str_replace($ds.$ds, $ds, $dir.$ds);
  }
  
  
  /*!
  ** Deletes a variable from the cache.
  **
  ** \param key
  **          \c string
  ** \param clear_php_cache
  **          \c bool - Clear php’s internal cache.
  **
  ** \throw Ninaca\Exceptions\InvalidArgumentException
  **     if \a key isn’t a string or is empty (the directory separator is
  **       considered as empty).
  ** \throw Ninaca\Exceptions\CacheException
  **     if no root directory has been defined.
  ** \throw Ninaca\Exceptions\FtpException
  **     if \a key can’t be deleted.
  */
  public function delete($key,
			 $clear_php_cache = true)
  {
    if (!is_string($key))
      throw new InvalidArgumentException(1, 'string', gettype($key));
    if (Misc::trim($key) === '' || $key === DIRECTORY_SEPARATOR)
      throw new InvalidArgumentException(1, 'nonempty string', 'empty string');
    
    if ($this->__root === '')
      throw new CacheException('You must set a root directory for '.
			       'caching files.');
    $key = $this->__root.$key.'.php';
    for ($i=0; $i<2; ++$i) {
      if (is_file($key)) {
	if (!is_writable($key))
	  throw new FtpException('$key is not writable.');
	try {
	  unlink($key);}
	catch (\ErrorException $e) {
	  throw new FtpException('$key is not removable.');}}
      $key .= '_ttl';}
    if ( $clear_php_cache )
      clearstatcache();
    return $ret;
  }
  
  
  /*!
  ** Gets a variable from the cache.
  ** 
  ** \param key
  **          \c string - Cache’s name.
  **
  ** \throw Ninaca\Exceptions\InvalidArgumentException
  **     if \a key isn’t a string or is empty (the directory separator is
  **       considered as empty).
  ** \throw Ninaca\Exceptions\CacheException
  **     if no root directory has been defined.
  ** \throw Ninaca\Exceptions\FtpException
  **     if \a key is not readable.
  **
  ** \return \c mixed - False, in case of problems.
  */
  public function get($key)
  {
    if (!is_string($key))
      throw new InvalidArgumentException(1, 'string', gettype($key));
    if (Misc::trim($key) === '' || $key === DIRECTORY_SEPARATOR)
      throw new InvalidArgumentException(1, 'nonempty string', 'empty string');
    
    if ($this->__root === '')
      throw new CacheException('You must set a root directory for '.
			       'caching files.');
    $key = $this->__root.$key.'.php';
    if (!is_readable($key))
      throw new FtpException('$key is not readable.');
    $this->checkTTL($key);
    $ret = include $key;
    return $ret === false ? false : unserialize($ret);
  }
  

  /*!
  ** Checks if a variable exists in the cache.
  ** 
  ** \param key
  **          \c string
  **
  ** \throw Ninaca\Exceptions\InvalidArgumentException
  **     if \a key isn’t a string or is empty (the directory separator is
  **       considered as empty).
  ** \throw Ninaca\Exceptions\CacheException
  **     if no root directory has been defined.
  ** 
  ** \return \c bool
  */
  public function exists($key)
  {
    if (!is_string($key))
      throw new InvalidArgumentException(1, 'string', gettype($key));
    if (Misc::trim($key) === '' || $key === DIRECTORY_SEPARATOR)
      throw new InvalidArgumentException(1, 'nonempty string', 'empty string');
    
    if ($this->__root === '')
      throw new CacheException('You must set a root directory for '.
			       'caching files.');
    $key = $this->__root.$key.'.php';
    $this->checkTTL($key);
    return is_file($key);
  }
  
  
  /*!
  ** Stores a variable in the cache.
  ** 
  ** \param key
  **          \c string
  ** \param var
  **          \c mixed - Variable to store.
  ** \param [lifetime]
  **          \c int - In second.
  ** \param [overwrite]
  **          \c bool - Overwrite existing data.
  **
  ** \throw Ninaca\Exceptions\InvalidArgumentException
  **     if \a key isn’t a string or is empty (the directory separator is
  **       considered as empty).
  ** \throw Ninaca\Exceptions\InvalidArgumentException
  **     if \a liftime isn’t an int.
  ** \throw Ninaca\Exceptions\CacheException
  **     if no root directory has been defined.
  ** \throw Ninaca\Exceptions\CacheException
  **     if the data can’t be stored.
  ** \throw Ninaca\Exceptions\FtpException
  **     if the cache can’t be created.
  ** 
  ** \return \c bool
  */
  public function store($key,
			$var,
			$lifetime = 0,
			$overwrite = true)
  {
    if (!is_string($key))
      throw new InvalidArgumentException(1, 'string', gettype($key));
    if (Misc::trim($key) === '' || $key === DIRECTORY_SEPARATOR)
      throw new InvalidArgumentException(1, 'nonempty string', 'empty string');
    if (!is_int($lifetime) && ctype_digit($lifetime))
      throw new InvalidArgumentException(3, 'int', gettype($lifetime));
    
    if ($this->__root === '')
      throw new CacheException('You must set a root directory for '.
			       'caching files.');    
    $key = $this->__root.$key.'.php';
    return $this->Store_exec($key, $var, $ttl, $overwrite);
  }


  /*!
  ** This method is a part of the store() method.
  ** 
  ** \copydoc store()
  */
  private function store_exec($key,
			      $var,
			      $ttl,
			      $overwrite)
  {
    if (!$overwrite && (is_file($key) && $this->checkTTL($key, false)))
      throw new CacheException("The cache $key already exists.", 1);
    $dir = mb_substr($key, 0, mb_strrpos($key, DIRECTORY_SEPARATOR));
    try {
      if (!is_dir($dir))
	Ftp::mkdir($dir);
      $fp = fopen($key, 'wb');
      $var = var_export(serialize($var), true);
      fwrite($fp, '<?php $var = '.$var.'; return $var ?>');
      fclose($fp);
      if (is_file($key.'_ttl'))
	unlink($key.'_ttl');
      if ($ttl)
	touch($key.'_ttl', $this->__time + $ttl);}
    catch (\ErrorException $e) {
      throw new FtpException($e->getMessage(), 1);}
    catch (FtpException $e) {
      throw new FtpException($e->getMessage(), 1);}
  }
  
  
  /*!
  ** Check if a cache file is still up-to-date.
  ** If the file has no TTL files, it will return true (no ttl files means
  ** the cache lives forever).
  **
  ** \param key
  **          \c string
  ** \param [del]
  **          \c bool - Define if we must delete the cache if it’s expired.
  **
  ** \return \c bool
  */
  private function checkTTL($key, $del = true)
  {
    if (!is_file($key.'_ttl') || filemtime($key.'_ttl') > $this->time)
      return true;
    else if ($del) {
      for ($i=0; $i<2; ++$i) {
	if (!is_writable($key))
	  throw new FtpException('$key is not writable.', 1);
	try {
	  unlink($key);}
	catch (\ErrorException $e) {
	  throw new FtpException('$key is not removable.', 1);}
	$key .= 'ttl';}
      clearstatcache();}
    return false;
  }
  
  
  /*!
  ** Deletes all cached data.
  **
  ** \throw Ninaca\Exceptions\CacheException
  **     if no root directory has been defined.
  */
  public function clearAll()
  {
    if ($this->__root === '')
      throw new CacheException('You must set a root directory for '.
			       'caching files.');
    return $this->clear($this->__root, true);
  }


  /*!
  ** Deletes all files from a cache directory
  ** 
  ** \param [dir]
  **          \c string
  ** \param [recursive]
  **          \c bool
  ** \param [pre]
  **          \c string
  ** \param [suf]
  **          \c string - Use a suffix for file (without extension).
  **
  ** \throw Ninaca\Exceptions\InvalidArgumentException
  **     if \a dir isn’t a string.
  ** \throw Ninaca\Exceptions\InvalidArgumentException
  **     if \a pre isn’t a string.
  ** \throw Ninaca\Exceptions\InvalidArgumentException
  **     if \a suf isn’t a string.
  ** \throw Ninaca\Exceptions\CacheException
  **     if a deletion fails.
  */
  public function clear($dir = '',
			$recursive = false,
			$pre = '',
			$suf = '')
  {
    if (!is_string($dir))
      throw new InvalidArgumentException(1, 'string', gettype($dir));
    if (!is_string($pre))
      throw new InvalidArgumentException(3, 'string', gettype($pre));
    if (!is_string($suf))
      throw new InvalidArgumentException(4, 'string', gettype($suf));
    
    if ($this->__root === '')
      throw new CacheException('You must set a root directory for '.
			       'caching files.');
    $this->clear_exec($dir, $recursive, $pre, $suf);
  }


  /*!
  ** This method is a part of the clear() method.
  ** 
  ** Deletes all files from a cache directory
  ** 
  ** \param [dir]
  **          \c string
  ** \param [recursive]
  **          \c bool
  ** \param [pre]
  **          \c string
  ** \param [suf]
  **          \c string - Use a suffix for file (without extension).
  ** \param [depth]
  **          \c int - depth for Debug::whoCalledMe().
  **
  ** \throw Ninaca\Exceptions\CacheException
  **     if a deletion fails.
  */
  private function clear_exec($dir = '',
			      $recursive = false,
			      $pre = '',
			      $suf = ''
			      $depth = 1)
  {
    $ds = DIRECTORY_SEPARATOR;
    $dir = $this->__root.$dir;
    $dir .= (substr($dir, -1) !== $ds) ? $ds : '';
    $folder = opendir($dir);
    $pre_s = mb_strlen($pre);
    $suf_s = mb_strlen($suf) + strlen('.php');
    $ret = true;
    while (($file = readdir($folder)) !== false) {
      if ($file !== '.' && $file !== '..') {
	if (is_dir($dir.$file) && $recursive){
	  $this->clear_exec($dir.$file, $recursive, $pre, $suf, $depth+1);
	  try{
	    rmdir($dir.$file);}
	  catch (\ErrorException $e){
	    throw new FtpException($e, $depth)}}
	else if (!is_dir($dir.$file) &&
		 ($pre === '' || mb_substr($file, 0, $pre_s) === $pre) &&
		 ($suf === '' || mb_substr($file, -$suf_s) === $suf.'.php'))
	  $this->delete($dir.$file, false)}}
    closedir($folder);
    clearstatcache();
    return $ret;
  }
}


