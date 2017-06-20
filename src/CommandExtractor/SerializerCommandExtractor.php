<?php
declare(strict_types=1);

namespace Eps\Req2CmdBundle\CommandExtractor;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;

class SerializerCommandExtractor implements CommandExtractorInterface
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * {@inheritdoc}
     * @throws \LogicException
     */
    public function extractFromRequest(Request $request, string $cmdClassName)
    {
        return $this->serializer->deserialize($request->getContent(), $cmdClassName, $request->getRequestFormat());
    }
}
