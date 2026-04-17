<?php

namespace App\Exceptions\HTTP;

use Throwable;
use App\Exceptions\Exception;

class HttpException extends Exception
{
    /**
     * The HTTP status code.
     *
     * @var array
     */
    public static array $messages = [
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden Access',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
    ];

    /**
     * HttpException constructor.
     */
    public function __construct(int $httpCode, ?string $message = null, ?Throwable $previous = null)
    {
        $this->code = $httpCode;
        parent::__construct($message ?? $this->getDefaultMessage($httpCode), $httpCode, $previous);
    }

    /**
     * Get the default message for
     * the given HTTP status code.
     */
    protected function getDefaultMessage(int $httpCode): string
    {
        return self::$messages[$httpCode] ?? 'Unknown Error';
    }

    /**
     * Get the HTTP status code.
     */
    public function getHttpCode(): int
    {
        return $this->getCode();
    }
}