<?php
declare(strict_types=1);

namespace Eps\Req2CmdBundle\Params\ParamCollector;

use Symfony\Component\HttpFoundation\Request;

interface ParamCollectorInterface
{
    public function collect(Request $request, array $propsMap): array;
}
