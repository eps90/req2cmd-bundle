<?php
declare(strict_types=1);

namespace Eps\Req2CmdBundle\Tests\Fixtures\Command;

final class DummyId
{
    /**
     * @var int
     */
    private $idValue;

    public function __construct(int $idValue)
    {
        $this->idValue = $idValue;
    }

    /**
     * @return int
     */
    public function getIdValue(): int
    {
        return $this->idValue;
    }
}
