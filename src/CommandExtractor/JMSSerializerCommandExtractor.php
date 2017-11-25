<?php
declare(strict_types=1);

namespace Eps\Req2CmdBundle\CommandExtractor;

use JMS\Serializer\ArrayTransformerInterface;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;

class JMSSerializerCommandExtractor implements CommandExtractorInterface
{
    /**
     * @var SerializerInterface
     */
    private $jmsSerializer;

    /**
     * @var ArrayTransformerInterface
     */
    private $jmsArrayTransformer;

    public function __construct(SerializerInterface $jmsSerializer, ArrayTransformerInterface $jmsArrayTransformer)
    {
        $this->jmsSerializer = $jmsSerializer;
        $this->jmsArrayTransformer = $jmsArrayTransformer;
    }

    /**
     * {@inheritdoc}
     * @throws \LogicException
     */
    public function extractFromRequest(Request $request, string $commandClass, array $additionalProps = [])
    {
        if (empty($request->getContent())) {
            return $this->jmsArrayTransformer->fromArray($additionalProps, $commandClass);
        }

        $decodedContent = $this->jmsSerializer->deserialize(
            $request->getContent(),
            'array',
            $request->getRequestFormat()
        );
        $finalProps = array_merge($decodedContent, $additionalProps);

        return $this->jmsArrayTransformer->fromArray($finalProps, $commandClass);
    }
}
