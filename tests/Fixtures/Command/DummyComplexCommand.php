<?php
declare(strict_types=1);

namespace Eps\Req2CmdBundle\Tests\Fixtures\Command;

final class DummyComplexCommand
{
    /**
     * @var DummyId
     */
    private $id;

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
     * @param DummyId $id
     * @param string $name
     * @param \DateTime $date
     */
    public function __construct(DummyId $id, string $name, \DateTime $date)
    {
        $this->id = $id;
        $this->name = $name;
        $this->date = $date;
    }

    /**
     * @return DummyId
     */
    public function getId(): DummyId
    {
        return $this->id;
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
