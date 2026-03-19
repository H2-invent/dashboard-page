<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class IconExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('app_icon', [$this, 'renderIcon'], ['is_safe' => ['html']]),
        ];
    }

    public function renderIcon(string $name): string
    {
        return match ($name) {
            'book-open' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-6 w-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5 5.013 5 3 5.967 3 7.16v10.59C3 18.944 5.013 20 7.5 20c1.746 0 3.332.477 4.5 1.253m0-15C13.168 5.477 14.754 5 16.5 5c2.487 0 4.5.967 4.5 2.16v10.59C21 18.944 18.987 20 16.5 20c-1.746 0-3.332.477-4.5 1.253" /></svg>',
            'lock-closed' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-6 w-6"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V7.875a4.5 4.5 0 1 0-9 0V10.5m-.75 0h10.5A2.25 2.25 0 0 1 19.5 12.75v5.25a2.25 2.25 0 0 1-2.25 2.25H6.75A2.25 2.25 0 0 1 4.5 18V12.75A2.25 2.25 0 0 1 6.75 10.5Z" /></svg>',
            'rocket-launch' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-6 w-6"><path stroke-linecap="round" stroke-linejoin="round" d="M15.59 14.37a6 6 0 0 0 4.84-5.2 12.05 12.05 0 0 0-6.09-5.63 12.03 12.03 0 0 0-5.63 6.09 6 6 0 0 0 5.2 4.84m0 0a6 6 0 0 1-1.2 2.35l-.88 1.17a1.5 1.5 0 0 1-2.34.13l-1.1-1.1a1.5 1.5 0 0 1 .13-2.34l1.17-.88a6 6 0 0 1 2.35-1.2m1.87 1.87 1.17.88a1.5 1.5 0 0 1 .13 2.34l-1.1 1.1a1.5 1.5 0 0 1-2.34-.13l-.88-1.17a6 6 0 0 1-1.2-2.35m-3.25 4.88 2.25-2.25m7.5-7.5L18 6" /></svg>',
            default => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-6 w-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>',
        };
    }
}
