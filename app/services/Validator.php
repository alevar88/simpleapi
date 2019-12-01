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
 * Class Validator
 * @package app\services
 */
class Validator implements ValidatorInterface
{

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $message;

    /**
     * @var callable
     */
    private $callable;

    /**
     * @var ValidationInterface
     */
    private $validation;

    /**
     * Validation constructor.
     * @param string $name
     * @param string $message
     * @param callable $callable
     */
    public function __construct(string $name, string $message, callable $callable)
    {
        $this->name = $name;
        $this->message = $message;
        $this->callable = $callable;
    }

    /**
     * @param ValidationInterface $validation
     * @return ValidatorInterface
     */
    public function __invoke(ValidationInterface $validation): ValidatorInterface
    {
        $this->validation = $validation;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public function validate($value): bool
    {
        return (bool)call_user_func_array($this->callable, array($value, $this->validation));
    }

}