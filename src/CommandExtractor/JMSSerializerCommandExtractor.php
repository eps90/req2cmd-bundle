<?php
declare(strict_types=1);

namespace Eps\Req2CmdBundle\CommandExtractor;

use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;

class JMSSerializerCommandExtractor implements CommandExtractorInterface
{
    /**
     * @var SerializerInterface
     */
    private $jmsSerializer;

    /**
     * JMSSerializerCommandExtractor constructor.
     * @param SerializerInterface $jmsSerializer
     */
    public function __construct(SerializerInterface $jmsSerializer)
    {
        $this->jmsSerializer = $jmsSerializer;
    }

    /**
     * {@inheritdoc}
     */
    public function extractFromRequest(Request $request, string $commandClass)
    {
        return $this->jmsSerializer->deserialize($request->getContent(), $commandClass, $request->getRequestFormat());
    }
}
