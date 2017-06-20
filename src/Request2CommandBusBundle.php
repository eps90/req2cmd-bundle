<?php
declare(strict_types=1);

namespace Eps\Request2CommandBusBundle;

use Eps\Request2CommandBusBundle\DependencyInjection\Request2CommandBusExtension;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class Request2CommandBusBundle extends Bundle
{
    public function getContainerExtension(): Extension
    {
        if ($this->extension === null) {
            $this->extension = new Request2CommandBusExtension();
        }

        return $this->extension;
    }
}
