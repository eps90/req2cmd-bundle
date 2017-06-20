<?php
declare(strict_types=1);

namespace Eps\Request2CommandBusBundle\Serializer;

use Eps\Request2CommandBusBundle\Command\DeserializableCommandInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class DeserializableCommandDenormalizer implements DenormalizerInterface
{
    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $class, $format = null, array $context = [])
    {
        return call_user_func($class . '::fromArray', $data);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null): bool
    {
        return is_subclass_of($type, DeserializableCommandInterface::class);
    }
}
