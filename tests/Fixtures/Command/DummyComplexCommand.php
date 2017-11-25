<?php
declare(strict_types=1);

namespace Eps\Req2CmdBundle\Tests\Fixtures\Command;

final class DummyComplexCommand
{
    /**
     * @var DummyId
     */
    private $dummyId;

    /**
     * @var string
     */
    private $name;

    /**
     * @var \DateTime
     */
    private $date;

    /**
     * DummyComplexCommand constructor.
     * @param DummyId $dummyId
     * @param string $name
     * @param \DateTime $date
     */
    public function __construct(?DummyId $dummyId, ?string $name, ?\DateTime $date)
    {
        $this->dummyId = $dummyId;
        $this->name = $name;
        $this->date = $date;
    }

    public static function withEmptyValues(): self
    {
        return new self(null, null, null);
    }

    /**
     * @return DummyId
     */
    public function getDummyId(): DummyId
    {
        return $this->dummyId;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return \DateTime
     */
    public function getDate(): \DateTime
    {
        return $this->date;
    }
}
