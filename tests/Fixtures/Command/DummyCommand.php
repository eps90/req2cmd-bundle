<?php
declare(strict_types=1);

namespace Eps\Req2CmdBundle\Tests\Fixtures\Command;

final class DummyCommand
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
}
