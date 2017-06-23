<?php
declare(strict_types=1);

namespace Eps\Req2CmdBundle\Params\ParamCollector;

use Eps\Req2CmdBundle\Params\ParameterMapper\ParamMapperInterface;
use Symfony\Component\HttpFoundation\Request;

class ParamCollector implements ParamCollectorInterface
{
    /**
     * @var ParamMapperInterface[]
     */
    private $mappers;

    public function __construct(array $mappers)
    {
        $this->mappers = [];
        foreach ($mappers as $mapper) {
            $this->addMapper($mapper);
        }
    }

    public function collect(Request $request, array $propsMap): array
    {
        return array_reduce(
            $this->mappers,
            function (array $carry, ParamMapperInterface $mapper) use ($request, $propsMap) {
                $carry = array_merge($carry, $mapper->map($request, $propsMap));
                return $carry;
            },
            []
        );
    }

    private function addMapper(ParamMapperInterface $mapper): void
    {
        $this->mappers[] = $mapper;
    }
}
