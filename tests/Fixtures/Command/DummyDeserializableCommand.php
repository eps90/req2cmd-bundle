<?php
declare(strict_types=1);

namespace Eps\Req2CmdBundle\Tests\Fixtures\Command;

use Eps\Req2CmdBundle\Command\DeserializableCommandInterface;

final class DummyDeserializableCommand implements DeserializableCommandInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $opts;

    public function __construct(string $name, array $opts)
    {
        $this->name = $name;
        $this->opts = $opts;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getOpts(): array
    {
        return $this->opts;
    }

    public static function fromArray(array $commandProps): self
    {
        return new self(
            $commandProps['name'] ?? '',
            $commandProps['opts'] ?? []
        );
    }
}
