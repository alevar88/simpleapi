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
 * Class Response
 * @package app\services
 */
class Response implements ResponseInterface
{

    /**
     * Errors messages
     */
    const
        E_CONTROLLER_NOT_FOUND = 'Controller not found',
        E_API_METHOD_NOT_FOUND = 'Method not found',
        E_HTTP_METHOD_NOT_ALLOWED = 'Method not allowed',
        E_NOT_FOUND = 'Not found',
        E_FORBIDDEN = 'Forbidden',
        E_BAD_TOKEN = 'Bad token',
        E_RECORD_ALREADY_EXISTS = 'Already exists',
        E_EMPTY_REQUEST_BODY = 'Request body is empty';

    /**
     * HTTP status text
     */
    const STATUS_TEXT = array(
        100 => 'Informational: Continue',
        101 => 'Informational: Switching Protocols',
        102 => 'Informational: Processing',
        200 => 'Successful: OK',
        201 => 'Successful: Created',
        202 => 'Successful: Accepted',
        203 => 'Successful: Non-Authoritative Information',
        204 => 'Successful: No Content',
        205 => 'Successful: Reset Content',
        206 => 'Successful: Partial Content',
        207 => 'Successful: Multi-Status',
        208 => 'Successful: Already Reported',
        226 => 'Successful: IM Used',
        300 => 'Redirection: Multiple Choices',
        301 => 'Redirection: Moved Permanently',
        302 => 'Redirection: Found',
        303 => 'Redirection: See Other',
        304 => 'Redirection: Not Modified',
        305 => 'Redirection: Use Proxy',
        306 => 'Redirection: Switch Proxy',
        307 => 'Redirection: Temporary Redirect',
        308 => 'Redirection: Permanent Redirect',
        400 => 'Client Error: Bad Request',
        401 => 'Client Error: Unauthorized',
        402 => 'Client Error: Payment Required',
        403 => 'Client Error: Forbidden',
        404 => 'Client Error: Not Found',
        405 => 'Client Error: Method Not Allowed',
        406 => 'Client Error: Not Acceptable',
        407 => 'Client Error: Proxy Authentication Required',
        408 => 'Client Error: Request Timeout',
        409 => 'Client Error: Conflict',
        410 => 'Client Error: Gone',
        411 => 'Client Error: Length Required',
        412 => 'Client Error: Precondition Failed',
        413 => 'Client Error: Request Entity Too Large',
        414 => 'Client Error: Request-URI Too Long',
        415 => 'Client Error: Unsupported Media Type',
        416 => 'Client Error: Requested Range Not Satisfiable',
        417 => 'Client Error: Expectation Failed',
        418 => 'Client Error: I\'m a teapot',
        419 => 'Client Error: Authentication Timeout',
        422 => 'Client Error: Unprocessable Entity',
        423 => 'Client Error: Locked',
        424 => 'Client Error: Failed Dependency',
        425 => 'Client Error: Unordered Collection',
        426 => 'Client Error: Upgrade Required',
        428 => 'Client Error: Precondition Required',
        429 => 'Client Error: Too Many Requests',
        431 => 'Client Error: Request Header Fields Too Large',
        444 => 'Client Error: No Response',
        449 => 'Client Error: Retry With',
        450 => 'Client Error: Blocked by Windows Parental Controls',
        451 => 'Client Error: Unavailable For Legal Reasons',
        494 => 'Client Error: Request Header Too Large',
        495 => 'Client Error: Cert Error',
        496 => 'Client Error: No Cert',
        497 => 'Client Error: HTTP to HTTPS',
        499 => 'Client Error: Client Closed Request',
        500 => 'Server Error: Internal Server Error',
        501 => 'Server Error: Not Implemented',
        502 => 'Server Error: Bad Gateway',
        503 => 'Server Error: Service Unavailable',
        504 => 'Server Error: Gateway Timeout',
        505 => 'Server Error: HTTP Version Not Supported',
        506 => 'Server Error: Variant Also Negotiates',
        507 => 'Server Error: Insufficient Storage',
        508 => 'Server Error: Loop Detected',
        509 => 'Server Error: Bandwidth Limit Exceeded',
        510 => 'Server Error: Not Extended',
        511 => 'Server Error: Network Authentication Required',
        598 => 'Server Error: Network read timeout error',
        599 => 'Server Error: Network connect timeout error',
    );

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var array
     */
    private $response = array(
        'requestId' => null,
        'requestMethod' => 'GET',
        'apiName' => null,
        'status' => 200,
        'errors' => array(),
        'response' => array(),
        'request' => array(),
    );

    /**
     * Response constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $request = $container->get('request');
        $this->response['requestId'] = $request->getRequestId();
        $this->response['requestMethod'] = $request->getMethod();
        $this->response['request'] = $request->getParams();
        $this->container = $container;
    }

    /**
     * @param int $code
     * @return ResponseInterface
     */
    public function withCode(int $code): ResponseInterface
    {
        $this->response['status'] = $code;
        return $this;
    }

    /**
     * @param array|string $error
     * @return ResponseInterface
     */
    public function withError($error): ResponseInterface
    {
        if (is_array($error)) {
            $this->response['errors'] = $error;
        } else {
            $this->response['errors'][] = $error;
        }
        return $this;
    }

    /**
     * @param array|string|int|bool|null $response
     * @return ResponseInterface
     */
    public function withResponse($response): ResponseInterface
    {
        $this->response['response'] = $response;
        return $this;
    }

    /**
     * @return array
     */
    public function getResponse(): array
    {
        return $this->response;
    }

    /**
     * @return string
     */
    public function sendNotFound(): string
    {
        throw new ResponseException(self::E_NOT_FOUND, 404);
    }

    /**
     * @return string
     */
    public function sendAlreadyExists(): string
    {
        throw new ResponseException(self::E_RECORD_ALREADY_EXISTS, 400);
    }

    /**
     * @return string
     */
    public function sendFailedValidate(): string
    {
        throw new ResponseException($this->container->get('validation')->getErrors(), 400);
    }

    /**
     * @param $value
     * @return string
     */
    public function sendResult($value): string
    {
        if (empty($value) || !$value) {
            $this->response['status'] = 400;
        }
        $this->response['response'] = $value;
        return $this->send();
    }

    /**
     * @return string
     */
    public function send(): string
    {
        $statusText = self::STATUS_TEXT[$this->response['status']] ?? 'Empty status text';
        header('Content-Type: application/json');
        header(sprintf('HTTP/1.1 %s %s', $this->response['status'], $statusText));
        $this->response['apiName'] = $this->container->get('request')->getApiName();
        $response = json_encode($this->response);
        if ($this->container->get('config')->logger['requests']) {
            $this->container->get('logger')->debug($response);
        }
        return $response;
    }

}