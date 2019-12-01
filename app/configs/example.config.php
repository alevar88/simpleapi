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

return array(
    'allowEmptyRequests' => false,
    'authorization' => array(
        'enable' => false,
        'header' => 'Authorization',
        'handler' => 'Your handler class name witch implement AuthorizationInterface',
    ),
    'logger' => array(
        'dir' => '/logs/',
        'name' => 'request.log',
        'requests' => false,
    ),
    'validators' => array(
        array('integer', 'Must be integer type', function ($value) {
            return (!is_array($value) && preg_match('/^\d+$/', "{$value}"));
        }),
        array('float', 'Must be float type', function ($value) {
            return (!is_array($value) && preg_match('/^\d+\.\d+$/', "{$value}"));
        }),
        array('alphanum', 'Must contains alpha or numbers', function ($value) {
            return (!is_array($value) && preg_match('/^[A-Za-zА-Яа-я0-9\s]+$/u', "{$value}"));
        }),
        array('alphanum_dash', 'Must contains alpha, numbers or dash', function ($value) {
            return (!is_array($value) && preg_match('/^[A-Za-zА-Яа-я0-9\s\-]+$/u', "{$value}"));
        }),
        array('required', 'Required', function ($value) {
            return (!is_array($value) && preg_match('/^.+$/', "{$value}"));
        }),
        array('array', 'Must be array type', function ($value) {
            return is_array($value);
        }),
    ),
    'router' => array(
        'escPath' => '',
        'routes' => array(
            '/path' => array(
                'handler' => 'Your controller class name',
                'rules' => array(
                    'default' => array(),
                    'get' => array(),
                    'post' => array(),
                    'put' => array(),
                    'delete' => array(),
                ),
            ),
        ),
    ),
    'commands' => array(),
    'database' => array(
        'dsn' => '',
        'username' => '',
        'password' => '',
        'options' => null,
        'onConnect' => function(\PDO $pdo) {}
    ),
);