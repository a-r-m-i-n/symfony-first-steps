<?php declare(strict_types = 1);
namespace App\Service;

use Psr\Log\LoggerInterface;

class RandomNumberGeneratorService
{
    private int $min;
    private int $max;
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger, int $min = 0, int $max = 100)
    {
        $this->min = $min;
        $this->max = $max;
        $this->logger = $logger;
    }

    public function generate()
    {
        $value = random_int($this->min, $this->max);

        $this->logger->debug(
            'Random number created: ' . $value,
            ['value' => $value, 'min' => $this->min, 'max' => $this->max]
        );

        return $value;
    }
}
