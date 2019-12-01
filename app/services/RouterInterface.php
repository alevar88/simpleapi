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
 * Interface RouterInterface
 * @package app\services
 */
interface RouterInterface
{

    /**
     * RouterInterface constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container);

    /**
     * @param callable $function
     * @return RouterInterface
     */
    public function beforeRun(callable $function): RouterInterface;

    /**
     * @param callable $function
     * @return RouterInterface
     */
    public function onShutdown(callable $function): RouterInterface;

    /**
     * @return void;
     */
    public function run();

}