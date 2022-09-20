<?php declare(strict_types = 1);
namespace App\EventListener;


use Armin\ExampleBundle\Event\CarrierFindAllEvent;

class CarrierFindAllEventListener
{
    public function __invoke(CarrierFindAllEvent $event)
    {
        $event->getQueryBuilder()->orderBy('c.id', 'DESC');
    }
}
