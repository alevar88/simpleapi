<?php
/**
 * This file is part of the "Simple RESTful-API PHP skeleton"
 *
 * @author Alexander Varnikov <alevar88@gmail.com>
 *
 * Project home: https://github.com/alevar88/simpleapi
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 *
 * @copyright Copyright 2019, Alexander Varnikov <alevar88@gmail.com>, Alexander Groza <ametiloray@gmail.com>
 *
 */

declare(strict_types = 1);

namespace app\services;

/**
 * Class Console
 * @package app\services
 */
class Console implements ConsoleInterface
{

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var array
     */
    private $commands = array();

    /**
     * Commands constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $commands = $container->get('config')->commands ?? array();
        foreach ($commands as $command) {
            $this->registerCommand(new $command($container));
        }
        $this->container = $container;
    }

    /**
     * @param CommandInterface $command
     * @return ConsoleInterface
     */
    public function registerCommand(CommandInterface $command): ConsoleInterface
    {
        $this->commands[$command->getName()] = $command;
        return $this;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasCommand(string $name): bool
    {
        return (isset($this->commands[$name]));
    }

    /**
     * @param string $name
     * @return CommandInterface
     */
    public function getCommand(string $name): CommandInterface
    {
        return $this->commands[$name] ?? null;
    }

    /**
     * @return bool
     */
    public function isEmptyCommands(): bool
    {
        return (empty($this->commands));
    }

    /**
     * @return array
     */
    private function getLongOptions(): array
    {
        $longOptions = array('commands', 'help', 'command:');
        foreach ($_SERVER['argv'] as $arg) {
            if (preg_match('/^--.+$/', $arg)) {
                $arg = str_replace('--', '', $arg);
                $longOptions[] = "{$arg}:";
            }
        }
        return $longOptions;
    }

    /**
     * Run command
     *
     * @return void
     */
    public function dispatch()
    {
        $options = getopt('', $this->getLongOptions());
        if (isset($options['commands'])) {
            echo 'Registered commands: ' . join(', ', array_keys($this->commands)) . PHP_EOL;
            exit(0);
        }
        $command = $options['command'] ?? '';
        if (!empty($command)) {
            unset($options['command']);
            if ($this->hasCommand($command)) {
                foreach ($options as $key => $value) {
                    $this->container->get('request')->{$key} = $value;
                }
                $command = $this->getCommand($command);
                if (isset($options['help'])) {
                    echo $command->getHelp() . PHP_EOL;
                    exit(0);
                }
                ob_start();
                $command->run();
                $output = ob_get_contents();
                ob_clean();
                if (!empty($output)) {
                    echo $output;
                }
                exit($command->getStatus());
            }
        }
        echo sprintf("Command %s not found\n", $command);
        exit(1);
    }

}