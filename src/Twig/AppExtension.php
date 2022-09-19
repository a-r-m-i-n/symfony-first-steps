<?php
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('reverseText', [$this, 'reverseText'], ['is_safe' => ['html']]),
        ];
    }

    public function reverseText(string $text, array $options = []): string
    {
        if (isset($options['highlight']) && $options['highlight']) {
            return '<em class="text-danger">' . strrev($text) . '</em>';
        }

        return strrev($text);
    }
}
