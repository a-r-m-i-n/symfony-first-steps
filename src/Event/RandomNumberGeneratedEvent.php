<?php declare(strict_types = 1);
namespace App\Event;

use Symfony\Contracts\EventDispatcher\Event;

class RandomNumberGeneratedEvent extends Event
{
    private int $generatedNumber;
    private bool $modified = false;

    public function __construct(int $generatedNumber)
    {
        $this->generatedNumber = $generatedNumber;
    }

    public function getGeneratedNumber(): int
    {
        return $this->generatedNumber;
    }

    public function setGeneratedNumber(int $generatedNumber): void
    {
        if ($generatedNumber !== $this->getGeneratedNumber()) {
            $this->modified = true;
        }
        $this->generatedNumber = $generatedNumber;
    }

    public function isModified(): bool
    {
        return $this->modified;
    }
}
