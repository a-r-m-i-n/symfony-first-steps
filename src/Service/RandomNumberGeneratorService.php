<?php declare(strict_types = 1);
namespace App\Service;

use App\Event\RandomNumberGeneratedEvent;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;

class RandomNumberGeneratorService
{
    private int $min;
    private int $max;
    private LoggerInterface $logger;
    private EventDispatcherInterface $dispatcher;

    public function __construct(LoggerInterface $logger, EventDispatcherInterface $dispatcher, int $min = 0, int $max = 100)
    {
        $this->min = $min;
        $this->max = $max;
        $this->logger = $logger;
        $this->dispatcher = $dispatcher;
    }

    public function generate()
    {
        $value = random_int($this->min, $this->max);

        /** @var RandomNumberGeneratedEvent $event */
        $event = $this->dispatcher->dispatch(new RandomNumberGeneratedEvent($value));

        $this->logger->debug(
            'Random number created: ' . $event->getGeneratedNumber(),
            ['value' => $event->getGeneratedNumber(), 'min' => $this->min, 'max' => $this->max, 'modifiedByEvent' => $event->isModified()]
        );

        return $event->getGeneratedNumber();
    }
}
