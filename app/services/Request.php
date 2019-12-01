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
 * Class Request
 * @package app\services
 */
class Request implements RequestInterface
{

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var array
     */
    private $request = array();

    /**
     * @var string
     */
    private $apiName = '';

    /**
     * @var string
     */
    private $requestId;

    /**
     * Request constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->requestId = uniqid(); //Generate unique request ID
        parse_str(file_get_contents('php://input'), $this->request);
    }

    /**
     * @param $name
     * @return mixed|null
     */
    public function __get($name)
    {
        return $this->request[$name] ?? null;
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function __set(string $name, $value)
    {
        $this->request[$name] = $value;
    }

    /**
     * @return bool
     */
    public function isEmptyRequest(): bool
    {
        return (empty($this->request));
    }

    /**
     * @param $name
     * @return bool
     */
    public function has($name): bool
    {
        return (isset($this->request[$name]));
    }

    /**
     * @param $name
     * @return bool
     */
    public function __isset($name): bool
    {
        return $this->has($name);
    }

    /**
     * @return string
     */
    public function getRequestId(): string
    {
        return $this->requestId;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return strtoupper($_SERVER['REQUEST_METHOD'] ?? '');
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    }

    /**
     * @param array $keys
     * @param array $filters
     * @return array
     */
    public function getParams(array $keys = array(), array $filters = array()): array
    {
        $result = array();
        foreach ($this->request as $key => $value) {
            if (!empty($keys) && !in_array($key, $keys)) {
                continue;
            }
            $result[$key] = $value;
        }
        return (!empty($filters)) ? filter_var_array($result, $filters) : $result;

    }

    /**
     * @param string $name
     * @param int $filter
     * @return mixed|null
     */
    public function getParam(string $name, int $filter = FILTER_DEFAULT)
    {
        $param = $this->request[$name] ?? null;
        return (!is_null($param)) ? filter_var($param, $filter) : $param;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        $header = $this->container->get('config')->authorization['header'] ?? 'Authorization';
        foreach (getallheaders() as $name => $value) {
            if ($name === $header) {
                $token = $value;
                break;
            }
        }
        return $token ?? '';
    }

    /**
     * @param string $name
     */
    public function setApiName(string $name)
    {
        $this->apiName = str_replace('/', '_', ltrim($name, '/'));;
    }

    /**
     * @return string
     */
    public function getApiName(): string
    {
        return $this->apiName;
    }

}