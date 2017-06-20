<?php
declare(strict_types=1);

namespace Eps\Req2CmdBundle\CommandExtractor;

use Symfony\Component\HttpFoundation\Request;

class MockCommandExtractor implements CommandExtractorInterface
{
    private $toReturn;

    /**
     * {@inheritdoc}
     */
    public function extractFromRequest(Request $request, string $cmdClassName)
    {
        return $this->toReturn;
    }

    public function willReturn($command): void
    {
        $this->toReturn = $command;
    }
}
