<?php
declare(strict_types=1);

namespace Eps\Request2CommandBusBundle\Command;

interface DeserializableCommandInterface
{
    public static function fromArray(array $commandProps);
}
