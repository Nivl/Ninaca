<?php

/*!
**  \file	Shell.php
**  \author	Nivl <nivl@free.fr>
**  \started	09/01/2009, 01:29 AM
**  \last	Nivl <nivl@free.fr> 06/07/2010, 02:08 AM
**  \copyright	Copyright (C) 2009 Laplanche Melvin
**  
**  Licensed under the MIT license:
**  <http://www.opensource.org/licenses/mit-license.php>.
**  For the full copyright and license information, please view the LICENSE
**  file that was distributed with this source code.
*/


namespace Ninaca\Console;
use \Ninaca\Configuration as Config;
use \Ninaca\Utilities\Ftp;


/*!
**  Application for executing tasks.
*/
abstract class Shell extends \Ninaca\CoreApplication
{
  protected
    $_tasks  = array(), ///< \c array
    $_output = null;    ///< \c ressource
  
  
  /*!
  ** Constructor
  */
  public function __construct(Output\Output $out = null)
  {
    if (PHP_SAPI <> 'cli')
      exit('The shell only works on CLI mode.');
    parent::__construct('cli', true);
    $this->_output = $out === null ? new Output\Std(): $out;
  }
  
  
  /*!
  ** Returns an array which contains tasks’ names and path.
  ** The \e keys are the names, the \e values are the path.
  **
  ** \return \c array
  */
  protected function _getTasksPath()
  {
    $list = array();
    foreach ($this->_configs['settings']['path'] as $ns => $infos) {
      if (!isset($infos['dirs']) || !is_array($infos['dirs']))
	continue;
      if (isset($infos['extensions']) && is_string($infos['extensions']))
	$infos['extensions'] = (array)$infos['extensions'];
      else
	$infos['extensions'] = array();
      if (isset($infos['exceptions']) && is_string($infos['exceptions']))
	$infos['exceptions'] = (array)$infos['exceptions'];
      else
	$infos['exceptions'] = array();
      $this->___getDirs_get($ns, $infos, $list); }
    return $list;
  }
  
  
  /*!
  ** Returns an array which contains tasks’ names and path.
  ** The \e keys are the names, the \e values are the path.
  ** This method is a part of the _getTasksPath() method.
  **
  ** \param ns
  **          \c string - Namespace for the tasks.
  ** \param infos
  **          \c array - Informations concerning the browsing.
  ** \param list
  **          \c &array - Variable which will contains the output.
  **
  */
  private function ___getTasksPath_get($ns,
				       array $infos,
				       array &$list)
  {
    foreach ($infos['dirs'] as $dir) {
      $closure = function($name, $path) use ($dir, $ns) {
	if (strpos($dir, ':ns') <> false){
	  $ds = DIRECTORY_SEPARATOR;
	  $end_ns = preg_replace('`^(.*)?\:ns/([a-z0-9_\\/-]+)$`i', '$2',$dir);
	  $new_ns = substr($path, 0, strpos($path, $end_ns) - 1);
	  if (($pos = strrpos($new_ns, $ds)) <> false)
	    $new_ns = substr($new_ns, $pos+1);
	  $sub = strstr($path, $new_ns.$ds.$end_ns);
	  $end_ns .= str_replace($new_ns.$ds.$end_ns, '',
				 substr($sub, 0, strrpos($sub, $ds)));
	  $out = $ns.'\\'.$new_ns.'\\'.$end_ns.'\\'.$name;}
	else
	  $out = str_replace('\\\\', '\\', $ns.'\\'.$name);
	return str_replace(array('\\\\', '/'), '\\', $out);};
      $path = str_replace(':ns', '*', $dir);
      Ftp::getFilesFromDir($path, $list, $infos['extensions'],
			   $infos['exceptions'], true, $closure);}
  }


  /*!
  ** Get the tasks.
  **
  ** \return \c array list of tasks.
  */
  protected function _getTasks()
  {
    $list = $this->_getTasksPath();
    $tasks = array();
    foreach ($list as $name => $path) {
      $Tmp = new '\\'.$name();
      if (!$Tmp->hasName())
	$Tmp->setName($name);
      if (!$Tmp->hasDescription())
	$Tmp->setDescription(_('No description.'));
      $new_name = ($Tmp->hasNamespace()) ? $Tmp->getNamespace().':' : null;
      $new_name .= $Tmp->getName();
      $tasks[$new_name] = $Tmp; }
    return $tasks;
  }
  

  /*!
  ** Run the application
  */
  public function run()
  {
    if ($_SERVER['argc'] < 2)
      TaskErrors::NoTasksFound();
    $task = &$_SERVER['argv'][1];
    if ($task === 'help')
      $this->displayHelp();
    else {
      if (!isset($this->tasks[$task]))
	TaskErrors::taskNotExists($task);
      for ( $i=2; $i < $_SERVER['argc']; ++$i ) {
	$size = strlen($_SERVER['argv'][$i]);
	if ( mb_substr($_SERVER['argv'][$i],0, 2) === '--' && $size > 2 )
	  $this->catchOption(mb_substr($_SERVER['argv'][$i],2), $task);
	else if ( $_SERVER['argv'][$i][0] === '-' && $size > 1 )
	  $this->catchShortcut(mb_substr($_SERVER['argv'][$i],1), $task);
	else
	  $this->catchArg($_SERVER['argv'][$i], $task); }
      $this->process($this->tasks[$task]); }
  }
  
  
  
  /*!
  ** Execute the task
  **
  ** \param Task task
  */
  protected function process($Task)
  {
    if ($Task->hasRequiredArguments())
      TaskErrors::argumentsMissing($Task->getRequiredArguments());
    
    $Task->execute();
  }
  
  
  
  /*
  ** Vérifie l'existence de l'option, et la met à jour.
  **
  ** @param string name
  ** @param string task [nom de la tâche]
  */
  private function catchOption($name, $task)
  {
    $info = explode('=', $name);
    
    if ($this->_tasks[$task]->hasOption($info[0])) {
      if (!$this->_tasks[$task]->getOption($info[0])->isBool() &&
	  !isset($info[1]))
	TaskErrors::optionWithoutValue($info[0]);

      $val = (isset($info[1])) ? $info[1] : true;
      $this->_tasks[$task]->getOption($info[0])->setValue($val); }
    else
      TaskErrors::optionNotExists($task, $info[0]);
  }
  
  
  
  /*
  ** Vérifie l'existence du raccourci, et met à jour l'option correspondante.
  **
  ** @param string name
  ** @param string task [nom de la tâche]
  */
  private function catchShortcut($name, $task)
  {
    $size = strlen($name);
    
    for ( $i=0; $i<$size; ++$i )
    {
      if ( $this->tasks[$task]->shortcutExists($name[$i]) )
	$this->tasks[$task]->getOptionFromShortcut($name[$i])->setValue(true);
      else
	TaskErrors::shortcutNotExists($task, $name[$i]);
    }
  }
  
  
  
  /*
  ** Ajoute un argument.
  **
  ** @param string value
  ** @param string task [nom de la tâche]
  */
  private function catchArg($value, $task)
  {
    $this->tasks[$task]->setArgumentsValue($value);
  }
  
  
  
  /*
  ** Affiche l'aide.
  */
  private function displayHelp()
  {
    if ( isset($_SERVER['argv'][2], $this->tasks[$_SERVER['argv'][2]]) )
      $this->tasks[$_SERVER['argv'][2]]->displayHelp();
    else
    {
      foreach ( $this->tasks as $name => $Task )
      {
	echo $name.PHP_EOL."\t".$Task->getDescription().PHP_EOL;
      }
    }
  }
}

