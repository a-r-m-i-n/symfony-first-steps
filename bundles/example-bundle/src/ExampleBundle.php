<?php declare(strict_types = 1);
namespace Armin\ExampleBundle;

use Armin\ExampleBundle\DependencyInjection\ExampleExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class ExampleBundle extends AbstractBundle
{
    public function getContainerExtension(): ?ExtensionInterface
    {
        return new ExampleExtension();
    }
}
