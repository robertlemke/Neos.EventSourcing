<?php
namespace Neos\Cqrs\Event;

/*
 * This file is part of the Neos.Cqrs package.
 *
 * (c) Contributors of the Neos Project - www.neos.io
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Neos\Cqrs\Event\Exception\EventBusException;
use Neos\Cqrs\EventListener\EventListenerContainer;
use Neos\Cqrs\EventListener\EventListenerLocator;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Log\SystemLoggerInterface;

/**
 * EventBus
 *
 * @Flow\Scope("singleton")
 */
class EventBus
{
    /**
     * @var EventListenerLocator
     * @Flow\Inject
     */
    protected $locator;

    /**
     * @var SystemLoggerInterface
     * @Flow\Inject
     */
    protected $logger;

    /**
     * @param EventTransport $transport
     * @throws \Exception
     */
    public function handle(EventTransport $transport)
    {
        $listeners = $this->locator->getListeners($transport->getEvent());

        if ($listeners === null) {
            return;
        }

        /** @var EventListenerContainer $listener */
        foreach ($listeners as $listener) {
            try {
                $listener->when($transport);
            } catch (\Exception $exception) {
                $this->logger->logException($exception);
                throw new EventBusException(sprintf('Event handler %s->%s threw an exception: %s (%s)', $listener->getListenerClass(), $listener->getListenerMethod(), $exception->getMessage(), $exception->getCode()), 1472675781, $exception);
            }
        }
    }
}
