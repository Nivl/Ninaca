<?php

/*!
**  \file	Apc.php
**  \author	Nivl <nivl@free.fr>
**  \started	07/29/2009, 03:54 PM
**  \last	Nivl <nivl@free.fr> 05/15/2010, 11:33 PM
**  \copyright	Copyright (C) 2009 Laplanche Melvin
**  
**  Licensed under the MIT license:
**  <http://www.opensource.org/licenses/mit-license.php>.
**  For the full copyright and license information, please view the LICENSE
**  file that was distributed with this source code.
*/


namespace Ninaca\Caches;
use Ninaca\Utilities\Misc;
use Ninaca\Exceptions\CacheException;
use Ninaca\Exceptions\InvalidArgumentException;


/*!
** Use APC to store data in cache.
*/
class Apc implements Cache
{
  /*!
  ** Class’ constructor
  **
  ** \throw Ninaca\Exceptions\CacheException if APC is not loaded.
  */
  public function __construct()
  {
    if (!extension_loaded('apc') || !function_exists('apc_add'))
      throw new CacheException('APC (>= 3.0.13) is not loaded.');
  }


  /*!
  ** Return the class’ id
  **
  ** \return \c string
  */
  public function getId()
  {
    return 'apc';
  }
  
  
  /*!
  ** Do nothing in this context.
  **
  ** \param dir
  **          \c string
  */
  public function setRootDir($dir){}
  
  
  /*!
  ** Deletes a variable from the cache.
  ** 
  ** \param key
  **          \c string
  ** 
  ** \throw Ninaca\Exceptions\InvalidArgumentException
  **     if \a key isn’t a string or is empty (the directory separator is
  **       considered as empty).
  ** \throw Ninaca\Exceptions\CacheException
  **     if \a key can’t be deleted.
  */
  public function delete($key)
  {
    if (!is_string($key))
      throw new InvalidArgumentException(1, 'string', gettype($key));
    if (Misc::trim($key) === '' || $key === DIRECTORY_SEPARATOR)
      throw new InvalidArgumentException(1, 'nonempty string', 'empty string');
    
    if (!apc_delete($key))
      throw new CacheException("$key can’t be deleted. Is $key exists?");
  }
  
  
  /*!
  ** Gets a variable from the cache.
  ** 
  ** \param key
  **          \c string
  **
  ** \throw Ninaca\Exceptions\InvalidArgumentException
  **     if \a key isn’t a string or is empty (the directory separator is
  **       considered as empty).
  ** \throw Ninaca\Exceptions\CacheException if \a key is not found.
  **
  ** \return mixed
  */
  public function get($key)
  {
    if (!is_string($key))
      throw new InvalidArgumentException(1, 'string', gettype($key));
    if (Misc::trim($key) === '' || $key === DIRECTORY_SEPARATOR)
      throw new InvalidArgumentException(1, 'nonempty string', 'empty string');
    
    $success = false;
    $return = apc_fetch($key, $success);
    if (!$success)
      throw new CacheException("The key $key hasn’t been found.");
    return $return;
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
  **     if \a key is not found.
  ** 
  ** \return bool
  */
  public function exists($key)
  {
    if (!is_string($key))
      throw new InvalidArgumentException(1, 'string', gettype($key));
    if (Misc::trim($key) === '' || $key === DIRECTORY_SEPARATOR)
      throw new InvalidArgumentException(1, 'nonempty string', 'empty string');
    
    $success = false;
    apc_fetch($key, $success);
    return $success;
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
  **         \c bool - Overwrite existing data.
  **
  ** \throw Ninaca\Exceptions\InvalidArgumentException
  **     if \a key isn’t a string or is empty (the directory separator is
  **       considered as empty).
  ** \throw Ninaca\Exceptions\InvalidArgumentException
  **     if \a liftime isn’t an int.
  ** \throw Ninaca\Exceptions\CacheException
  **     if the data can’t be stored.
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

    $func = $overwrite ? 'apc_store' : 'apc_add';
    if (!$func($key, $var, $lifetime)) {
      if ($overwrite)
	throw new CacheException("The key $key can’t be stored for an ".
				 'unknown reason.');
      else
	throw new CacheException("The key $key already exists, or something ".
				 'wrong happened for an unknown reason.');}
  }
  
  
  /*!
  ** Deletes all cached data.
  **
  ** \throw Ninaca\Exceptions\CacheException if \a key is not found.
  */
  public function clearAll()
  {
    if (!apc_clear_cache('user'))
      throw new CacheException('Something wrong happened for an unknown '.
			       'reason. The cache hasn’t been cleared.');
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
  **          \c string
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
    
    $dir .= mb_substr($dir, -1) !== '/' && !empty($dir) ? '/' : '';
    $cacheInfo = apc_cache_info('user');
    if (empty($cacheInfo['cache_list']))
      return '';
    foreach ($cacheInfo['cache_list'] as $info) {
      $tmp = str_replace($dir, '', $info['info']);
      if ($tmp !== $info['info'] || empty($dir)) {
	if ($recursive || mb_strpos($tmp,'/') === false) {
	  $key = mb_strrpos($info['info'], '/');
	  $key = str_replace('/', '', mb_substr($info['info'], $key));
	  if (($pre === '' || mb_substr($key, 0, mb_strlen($pre)) === $pre) &&
	      ($suf === '' || mb_substr($key, -mb_strlen($suf)) === $suf))
	    if (!apc_delete($info['info']))
	      throw new CacheException("The key {$info['info']} can’t be ".
				       'deleted for an unknown reason.');}}}
  }
}
