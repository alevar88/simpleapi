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

namespace PHPSTORM_META {

    use app\services\AuthorizationInterface;
    use app\services\ConfigInterface;
    use app\services\ConsoleInterface;
    use app\services\ContainerInterface;
    use app\services\DatabaseInterface;
    use app\services\LoggerInterface;
    use app\services\ResponseInterface;
    use app\services\ValidationInterface;
    use app\services\ConsoleInterface;

    override(ContainerInterface::get(0), map(array(
        'config' => app\services\ConfigInterface::class,
        'request' => app\services\RequestInterface::class,
        'response' => app\services\ResponseInterface::class,
        'validation' => app\services\ValidationInterface::class,
        'database' => app\services\DatabaseInterface::class,
        'authorization' => app\services\AuthorizationInterface::class,
        'logger' => app\services\LoggerInterface::class,
        'console' => app\services\ConsoleInterface::class,
    )));

}