<?php
declare(strict_types=1);

namespace Eps\Req2CmdBundle\CommandExtractor;

use Symfony\Component\HttpFoundation\Request;

class MockCommandExtractor implements CommandExtractorInterface
{
    private $toReturn;
    private $toReturnForArgs;
    private $lastArgsHash;

    /**
     * MockCommandExtractor constructor.
     */
    public function __construct()
    {
        $this->toReturnForArgs = [];
    }

    /**
     * {@inheritdoc}
     */
    public function extractFromRequest(Request $request, string $commandClass, array $additionalProps = [])
    {
        $paramsHash = serialize([$request, $commandClass, $additionalProps]);
        if (array_key_exists($paramsHash, $this->toReturnForArgs)) {
            return $this->toReturnForArgs[$paramsHash];
        }

        return $this->toReturn;
    }

    public function willReturn($command): self
    {
        if ($this->lastArgsHash !== null) {
            $this->toReturnForArgs[$this->lastArgsHash] = $command;
            return $this;
        }

        $this->toReturn = $command;

        return $this;
    }

    public function andThen(): self
    {
        return $this;
    }

    public function forArguments(Request $request, string $cmdClass, array $additionalProps = []): self
    {
        $this->lastArgsHash = serialize([$request, $cmdClass, $additionalProps]);

        return $this;
    }
}
