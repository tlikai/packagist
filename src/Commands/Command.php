<?php

namespace Packagist\Commands;

use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

class Command extends BaseCommand
{
    /**
     * The command name
     *
     * @var string
     */
    protected $name;

    /**
     * The command description
     *
     * @var string
     */
    protected $description;

    /**
     * The console input
     *
	 * @var Symfony\Component\Console\Output\OutputInterface
     */
    protected $input;

    /**
     * The console output
     *
	 * @var Symfony\Component\Console\Output\OutputInterface
     */
    protected $output;

    /**
     * @param string $name
     */
    public function __construct()
    {
        parent::__construct($this->name);

        $this->setDescription($this->description);

        foreach ($this->getArguments() as $argument) {
			call_user_func_array(array($this, 'addArgument'), $argument);
        }

        foreach ($this->getOptions() as $option) {
			call_user_func_array(array($this, 'addOption'), $option);
        }
    }

    /**
     * Execute the command
     *
	 * @param Symfony\Component\Console\Input\InputInterface $input
	 * @param Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return mixed
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;

        return $this->fire();
    }

    /**
     * Execute the command
     *
     * @throws \LogicException When this abstract method is not implemented
     *
     * @return null|integer
     */
    public function fire()
    {
        throw new \LogicException('You must override the fire() method in the concrete command class.');
    }

    /**
     * Call command
     *
     * @param string $command command name
     * @param array $parameters
     *
     * @return integer
     */
    public function call($command, array $parameters = array())
    {
        $instance = $this->getApplication()->find($command);
        $arguments['command'] = $command;
		return $instance->run(new ArrayInput($arguments), $this->output);
    }

	/**
	 * Get the command arguments definition.
	 *
	 * @return array
	 */
    protected function getArguments()
    {
        return array();
    }

	/**
	 * Get the command options definition.
	 *
	 * @return array
	 */
    protected function getOptions()
    {
        return array();
    }

    /**
     * Get the argument
     *
     * @var mixed
     */
    public function getArgument($name)
    {
        return $this->input->getArgument($name);
    }

    /**
     * Get the option
     *
     * @var mixed
     */
    public function getOption($name)
    {
        return $this->input->getOption($name);
    }

	/**
	 * Prompt
	 *
	 * @param string $message
	 * @param string $default
	 * @return string
	 */
	public function prompt($message, $default = null)
	{
		$dialog = $this->getHelperSet()->get('dialog');
		return $dialog->ask($this->output, "<prompt>$question</prompt>", $default);
	}

	/**
	 * Confirm a question
	 *
	 * @param string $message
	 * @param bool $default
	 * @return bool
	 */
	public function confirm($message, $default = true)
	{
		$dialog = $this->getHelperSet()->get('dialog');
		return $dialog->askConfirmation($this->output, "<confirm>$message</confirm>", $default);
	}

	/**
	 * Write a stdout
	 *
	 * @param string $message
	 * @return void
	 */
	public function write($message, $tag = null)
	{
        if ($tag) {
            $message = "<$tag>$message</$tag>";
        }
		$this->output->writeln($message);
	}

	/**
	 * Write a stderr
	 *
	 * @param string $message
	 * @return void
	 */
	public function error($message)
	{
        $this->write($message, 'error');
	}

	/**
	 * Write a warning
	 *
	 * @param string $message
	 * @return void
	 */
	public function warning($message)
	{
        $this->write($message, 'warning');
	}

	/**
	 * Write a info
	 *
	 * @param string $message
	 * @return void
	 */
	public function info($message)
	{
        $this->write($message, 'info');
	}
}
