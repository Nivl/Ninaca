<?php

/*!
**  \package	Task.php
**  \author	Nivl <nivl@free.fr>
**  \started	09/02/2009, 04:56 PM
**  \last	Nivl <nivl@free.fr> 07/07/2010, 01:05 AM
**  \copyright	Copyright (C) 2009 Laplanche Melvin
**
**  Licensed under the MIT license:
**  <http://www.opensource.org/licenses/mit-license.php>.
**  For the full copyright and license information, please view the LICENSE
**  file that was distributed with this source code.
*/


namespace Ninaca/Console;


/*!
** Main class to create a task.
*/
abstract class Task
{
  protected
    $name	= null,
    $options	= array(),
    $namespace	= null,
    $shortcuts	= array(),
    $arguments	= array(),
    $description	= null,
    $requiredArguments	= array();

  abstract protected function configure();
  abstract public function execute();

  public function __construct()
  {
    $this->configure();
  }


  /*!
  ** Adds an option.
  **
  ** \param Option
  **          \c Option
  */
  public function addOption(Inputs\Option $Option)
  {
    $name = $Option->getName();

    if ($this->optionExists($name))
      TaskErrors::duplicatedOption($name);
    $this->options[$name] = $Option;
    if ($Option->hasShortcut()) {
      $shortcut = $Option->getShortcut();
      if ($this->shortcutExists($shortcut))
	TaskErrors::duplicatedShortcut($shortcut);
      $this->shortcuts[$shortcut] = $Option;}
  }


  /*!
  ** Adds options.
  **
  ** \param options
  **          \c array - Tableau d'instance d’Option
  */
  public function addOptions(array $options)
  {
    foreach ($options as $Option) {
      if ($Option instanceof Inputs\Option)
	$this->addOption($Option);
      else
	TaskErrors::badOptionType();
    }
  }



  /*!
  ** Adds an argument.
  **
  ** \param Arg
  **          \c array - Tableau d'instance d’Argument
  */
  public function addArgument(TaskArgument $Arg)
  {
    $name = $Arg->getName();
    $this->arguments[$name] = $Arg;
    if ($Arg->isRequired())
      $this->requiredArguments[$name] = true;
  }


  /*!
  ** Adds arguments.
  **
  ** \param arg
  **          \c array - Tableau d'instance d’Argument
  */
  public function addArguments(array $args)
  {
    foreach ($args as $Arg)
    {
      if ( $Arg instanceof Arguments )
	$this->addArgument($Arg);
      else
	TaskErrors::badArgumentType();
    }
  }



  /*
  ** Check if the task has a namespace
  **
  ** @return bool
  */
  public function hasNamespace()
  {
    return !Misc::isEmpty($this->namespace);
  }



  /*
  ** Vérifie si la tâche a un nom.
  **
  ** @return bool
  */
  public function hasName()
  {
    return !Misc::isEmpty($this->name);
  }



  /*
  ** Vérifie si la tâche a une description.
  **
  ** @return bool
  */
  public function hasDescription()
  {
    return !Misc::isEmpty($this->description);
  }



  /*
  ** Vérifie si la tâche a des options.
  **
  ** @return bool
  */
  public function hasOption()
  {
    return !empty($this->options);
  }



  /*
  ** Vérifie si la tâche a des arguments.
  **
  ** @return bool
  */
  public function hasArgument()
  {
    return !empty($this->arguments);
  }



  /*
  ** Vérifie si la tâche a des arguments requis qui n'ont pas été saisie.
  **
  ** @return bool
  */
  public function hasRequiredArguments()
  {
    return !empty($this->requiredArguments);
  }



  /*
  ** Retourne le namespace de la tâche.
  **
  ** @return string
  */
  public function getNamespace()
  {
    return $this->namespace;
  }



  /*
  ** Retourne le nom de la tâche.
  **
  ** @return string
  */
  public function getName()
  {
    return $this->name;
  }



  /*
  ** Retourne la description de la tâche.
  **
  ** @return string
  */
  public function getDescription()
  {
    return $this->description;
  }



  /*
  ** Retourne une option.
  **
  ** @param string option_name
  **
  ** @return TaskOption
  */
  public function getOption($option_name)
  {
    if ( $this->optionExists($option_name) )
      return $this->options[$option_name];

    TaskErrors::optionNotExists($this->name, $option_name);
  }



  /*
  ** Retourne la valeur d'une option.
  **
  ** @param string option_name
  **
  ** @return mixed
  */
  public function getOptionsValue($option_name)
  {
    if ( $this->optionExists($option_name) )
      return $this->options[$option_name]->getValue();

    TaskErrors::optionNotExists($this->name, $option_name);
  }



  /*
  ** Retourne la valeur d'un argument.
  **
  ** @param string argument_name
  **
  ** @return mixed
  */
  public function getArgumentsValue($argument_name)
  {
    if ( $this->argumentExists($argument_name) )
      return $this->arguments[$argument_name]->getValue();

    TaskErrors::argumentNotExists($this->name, $argument_name);
  }



  /*
  ** Retourne une option à partir de son raccourci.
  **
  ** @param string shortcut.
  **
  ** @return TaskOption
  */
  public function getOptionFromShortcut($shortcut)
  {
    if ( $this->shortcutExists($shortcut) )
      return $this->shortcuts[$shortcut];

    TaskErrors::shortcutNotExists($this->name, $shortcut);
  }


  /*
  ** Vérifie l'existence d'un argument.
  **
  ** @param string argument_name
  **
  ** @return bool
  */
  public function argumentExists($argument_name)
  {
    return !empty($this->arguments[$argument_name]);
  }



  /*
  ** Vérifie l'existence d'une option.
  **
  ** @param string option_name
  **
  ** @return bool
  */
  public function optionExists($option_name)
  {
    return !empty($this->options[$option_name]);
  }



  /*
  ** Vérifie l'existence d'un raccourci.
  **
  ** @param string shortcut_name
  **
  ** @return bool
  */
  public function shortcutExists($shortcut_name)
  {
    return !empty($this->shortcuts[$shortcut_name]);
  }


  /*
  ** Retourne le nombre d'argument.
  **
  ** @param int type
  **
  ** @return int
  */
  public function getNumberOfArguments($type = null)
  {
    if ( $type === TaskArgument::OPTIONAL )
      return count($this->arguments) - count($this->requiredArguments);
    else if ( $type === TaskArgument::REQUIRED )
      return count($this->requiredArguments);
    else
      return count($this->arguments);
  }



  /*
  ** Retourne l'argument courrant.
  **
  ** @return TaskArgument
  */
  public function setArgumentsValue($value)
  {
    if ( $this->hasArgument() && !$this->getCurrentArgument()->hasValue() )
      $Arg = $this->getCurrentArgument();
    else if ( ($Arg = $this->getNextArgument()) === false )
      TaskErrors::tooManyArguments($this->getNumberOfArguments());

    $Arg->setValue($value);
    $name = $Arg->getName();

    if ( isset($this->requiredArguments[$name]) )
      unset($this->requiredArguments[$name]);
  }



  /*
  ** Définit un nom pour la tâche si elle n'en a pas déjà un.
  */
  public function setName($name)
  {
    if ( !$this->hasName() )
      $this->name = $name;
  }



  /*
  ** Définit une description pour la tâche si elle n'en a pas déjà un.
  */
  public function setDescription($description)
  {
    if ( !$this->hasDescription() )
      $this->description = $description;
  }



  /*
  ** Retourne la liste des arguments requis.
  **
  ** @return
  */
  public function getRequiredArguments()
  {
    return array_keys($this->requiredArguments);
  }


  /*
  ** Retourne l'argument courrant.
  **
  ** @return TaskArgument
  */
  public function getCurrentArgument()
  {
    return current($this->arguments);
  }


  /*
  ** Retourne l'argument suivant.
  **
  ** @return TaskArgument
  */
  public function getNextArgument()
  {
    return next($this->arguments);
  }



  /*
  ** Affiche l'aide.
  */
  public function displayHelp()
  {
    echo $this->getSynopsys();
    echo PHP_EOL.PHP_EOL;
    echo String::wrap80($this->description);
    echo PHP_EOL.PHP_EOL;
    echo $this->getArgInfo();
    echo PHP_EOL.PHP_EOL;
    echo $this->getOptInfo();
    echo PHP_EOL.PHP_EOL;
  }



  /*
  ** Rtourne le synopsys de la tâche.
  **
  ** @return string
  */
  private function getSynopsys()
  {
    $name = ($this->hasNamespace()) ? $this->namespace.':' : null;
    $name .= $this->name;
    $output = _('Usage: ').$name;

    if ( $this->hasOption() )
      $output .= ' '._('[option(s)]');

    foreach ( $this->arguments as $key => $Arg )
    {
      if ( $Arg->isRequired() )
	$output .= ' '.$Arg->getName();
      else
	$output .= ' ['.$Arg->getName().']';
    }

    return $output;
  }



  /*
  ** retourne la liste des arguments.
  **
  ** @return string
  */
  private function getArgInfo()
  {
    if ( !$this->hasArgument() )
      return _('This task takes no arguments.');

    $arg_info = _('Arguments’ list: ');
    $args = array();

    foreach ( $this->arguments as $key => $Arg )
      $args[$Arg->getName()] = $Arg->getDescription();

    return $arg_info.Arrays::PrintInCLI($args);
  }



  /*
  ** retourne la liste des arguments.
  **
  ** @return string
  */
  private function getOptInfo()
  {
    if ( !$this->hasOption() )
      return _('This task has no options.');

    $opt_info = _('Options’ list: ');
    $opts = array();

    foreach ( $this->options as $key => $Opt )
    {
      if ( $Opt->hasShortcut() )
	$name = '-'.$Opt->getShortcut().' --'.$Opt->getName();
      else
	$name = '--'.$Opt->getName();

      $opts[$name] = $Opt->getDescription();
    }

    return $opt_info.Arrays::PrintInCLI($opts);
  }
}

