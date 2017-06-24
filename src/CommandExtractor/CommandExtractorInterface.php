<?php
declare(strict_types=1);

namespace Eps\Req2CmdBundle\CommandExtractor;

use Symfony\Component\HttpFoundation\Request;

interface CommandExtractorInterface
{
    /**
     * @param Request $request
     * @param string $commandClass
     * @param array $additionalProps
     * @return mixed
     */
    public function extractFromRequest(Request $request, string $commandClass, array $additionalProps = []);
}
