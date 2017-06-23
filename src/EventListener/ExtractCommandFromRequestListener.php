<?php
declare(strict_types=1);

namespace Eps\Req2CmdBundle\EventListener;

use Eps\Req2CmdBundle\CommandExtractor\CommandExtractorInterface;
use Eps\Req2CmdBundle\Params\ParamCollector\ParamCollectorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class ExtractCommandFromRequestListener
{
    public const API_RESPONDER_CONTROLLER = 'eps.req2cmd.action.api_responder';
    public const CMD_CLASS_PARAM = '_command_class';
    public const CMD_PARAM = '_command';
    public const CMD_PROPS_PARAM = '_command_properties';
    private const CONTROLLER_PARAM = '_controller';

    /**
     * @var CommandExtractorInterface
     */
    private $extractor;

    /**
     * @var ParamCollectorInterface
     */
    private $paramCollector;

    public function __construct(CommandExtractorInterface $extractor, ParamCollectorInterface $paramCollector)
    {
        $this->extractor = $extractor;
        $this->paramCollector = $paramCollector;
    }

    public function onKernelRequest(GetResponseEvent $event): void
    {
        $request = $event->getRequest();
        $commandClass = $request->attributes->get(self::CMD_CLASS_PARAM);
        if ($commandClass === null) {
            return;
        }

        $additionalParams = $this->extractAdditionalParams($request);
        $command = $this->extractor->extractFromRequest($request, $commandClass, $additionalParams);

        $request->attributes->set(self::CMD_PARAM, $command);
        $request->attributes->remove(self::CMD_CLASS_PARAM);

        if (!$request->attributes->has(self::CONTROLLER_PARAM)) {
            $request->attributes->set(self::CONTROLLER_PARAM, self::API_RESPONDER_CONTROLLER);
        }
    }

    private function extractAdditionalParams(Request $request): array
    {
        $additionalParams = [];
        if ($request->attributes->has(self::CMD_PROPS_PARAM)) {
            $additionalProps = $request->attributes->get(self::CMD_PROPS_PARAM);
            $additionalParams = $this->paramCollector->collect($request, $additionalProps);
        }

        return $additionalParams;
    }
}
