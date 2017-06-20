<?php
declare(strict_types=1);

namespace Eps\Req2CmdBundle\EventListener;

use Eps\Req2CmdBundle\CommandExtractor\CommandExtractorInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class ExtractCommandFromRequestListener
{
    public const API_RESPONDER_CONTROLLER = 'eps.req2cmd.action.api_responder';
    public const CMD_CLASS_PARAM = '_command_class';
    public const CMD_PARAM = '_command';
    private const CONTROLLER_PARAM = '_controller';

    /**
     * @var CommandExtractorInterface
     */
    private $extractor;

    public function __construct(CommandExtractorInterface $extractor)
    {
        $this->extractor = $extractor;
    }

    public function onKernelRequest(GetResponseEvent $event): void
    {
        $request = $event->getRequest();
        $commandClass = $request->attributes->get(self::CMD_CLASS_PARAM);
        if ($commandClass === null) {
            return;
        }

        $command = $this->extractor->extractFromRequest($request, $commandClass);

        $request->attributes->set(self::CMD_PARAM, $command);
        $request->attributes->remove(self::CMD_CLASS_PARAM);

        if (!$request->attributes->has(self::CONTROLLER_PARAM)) {
            $request->attributes->set(self::CONTROLLER_PARAM, self::API_RESPONDER_CONTROLLER);
        }
    }
}
