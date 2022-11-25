<?php

declare(strict_types=1);

namespace App\Domain\Service\Exception;


abstract class AbstractNotFoundException extends \Exception
{
    private array $params;

    public function __construct(array $params = [], $code = 0, \Throwable $previous = null)
    {
        parent::__construct($this->getDefaultMessage(), $code, $previous);
        $this->params = $params;
    }

    abstract public function getDefaultMessage(): string;

    public function getParam(string $key)
    {
        return $this->params[$key] ?? null;
    }
}
