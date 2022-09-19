<?php declare(strict_types = 1);
namespace App\EventListener;


use App\Event\RandomNumberGeneratedEvent;

class NoRandomNumberListener
{
    public function __invoke(RandomNumberGeneratedEvent $event)
    {
        // Always set generated number to "42"
        $event->setGeneratedNumber(42);
    }
}
