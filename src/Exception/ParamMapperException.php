<?php
declare(strict_types=1);

namespace Eps\Req2CmdBundle\Exception;

use JMS\Serializer\Exception\LogicException;

final class ParamMapperException extends LogicException implements Req2CmdExceptionInterface
{
    public static function noParamFound(string $paramName, \Throwable $previous = null): self
    {
        $excCode = 101;
        $message = sprintf('Parameter "%s" is required and it\'s missing in your request', $paramName);
        return new self($message, $excCode, $previous);
    }

    public static function paramEmpty(string $paramName, \Throwable $previous = null): self
    {
        $excCode = 102;
        $message = sprintf('The value of the parameter "%s" cannot be empty', $paramName);
        return new self($message, $excCode, $previous);
    }
}
