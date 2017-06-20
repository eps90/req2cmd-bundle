<?php
declare(strict_types=1);

namespace Eps\Request2CommandBusBundle\CommandExtractor;

use Symfony\Component\HttpFoundation\Request;

interface CommandExtractorInterface
{
    /**
     * @param Request $request
     * @param string $commandClass
     * @return object
     */
    public function extractFromRequest(Request $request, string $commandClass);
}
