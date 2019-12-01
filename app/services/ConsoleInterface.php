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
 * Interface ConsoleInterface
 * @package app\services
 */
interface ConsoleInterface
{

    /**
     * ConsoleInterface constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container);

    /**
     * @param CommandInterface $command
     * @return ConsoleInterface
     */
    public function registerCommand(CommandInterface $command): ConsoleInterface;

    /**
     * @param string $name
     * @return bool
     */
    public function hasCommand(string $name): bool;

    /**
     * @param string $name
     * @return CommandInterface
     */
    public function getCommand(string $name): CommandInterface;

    /**
     * @return bool
     */
    public function isEmptyCommands(): bool;

    /**
     * @return void
     */
    public function dispatch();

}