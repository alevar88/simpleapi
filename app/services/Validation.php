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
 * Class Validation
 * @package app\services
 */
class Validation implements ValidationInterface
{

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var array
     */
    private $validators = array();

    /**
     * @var array
     */
    private $errors = array();

    /**
     * @var array
     */
    private $rules = array();

    /**
     * @var array
     */
    private $params = array();

    /**
     * Validation constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->params = $container->get('request')->getParams();
        $this->container = $container;
    }

    /**
     * @param ValidatorInterface $validator
     */
    public function registerValidator(ValidatorInterface $validator)
    {
        $this->validators[$validator->getName()] = $validator($this);
    }

    /**
     * @param string $name
     * @return mixed|null
     */
    public function getValidator(string $name)
    {
        return $this->validators[$name] ?? null;
    }

    /**
     * @param string $key
     * @param string $message
     */
    private function addError(string $key, string $message)
    {
        $this->errors[$key] = $message;
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @param array $rules
     * @return ValidationInterface
     */
    public function withRules(array $rules = array()): ValidationInterface
    {
        if (!empty($rules)) {
            $methods = array_map('strtolower', Router::ALLOWED_HTTP_METHODS);
            array_push($methods, 'default');
            foreach ($rules as $key => $value) {
                if (!in_array($key, $methods)) {
                    $this->rules[$key] = $value;
                } elseif (in_array($key, array('default', strtolower($this->container->get('request')->getMethod())))) {
                    $this->rules = array_merge($this->rules, $value);
                }
            }
        }
        return $this;
    }

    /**
     * @param array $params
     * @return ValidationInterface
     */
    public function withParams(array $params): ValidationInterface
    {
        $this->params = $params;
        return $this;
    }

    /**
     * @return bool
     */
    public function validate(): bool
    {
        if (!empty($this->params)) {
            foreach ($this->rules as $key => $value) {
                $rules = explode('|', $value);
                foreach($rules as $rule) {
                    $validator = $this->getValidator($rule);
                    if ($validator instanceof ValidatorInterface) {
                        if (isset($this->params[$key])) {
                            if (!$validator->validate($this->params[$key])) {
                                $this->addError($key, $validator->getMessage());
                                continue 2;
                            }
                        } elseif ($value === 'required') {
                            $this->addError($key, $validator->getMessage());
                        }
                    }
                }
            }
        }
        return (empty($this->getErrors()));
    }

}