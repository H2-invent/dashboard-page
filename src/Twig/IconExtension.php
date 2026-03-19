<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class IconExtension extends AbstractExtension
{
    /**
     * @var array<string, string>
     */
    private const ICONS = [
        'book-open' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-6 w-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5 5.013 5 3 5.967 3 7.16v10.59C3 18.944 5.013 20 7.5 20c1.746 0 3.332.477 4.5 1.253m0-15C13.168 5.477 14.754 5 16.5 5c2.487 0 4.5.967 4.5 2.16v10.59C21 18.944 18.987 20 16.5 20c-1.746 0-3.332.477-4.5 1.253" /></svg>',
        'briefcase' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-6 w-6"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 7.5V6a2.25 2.25 0 0 1 2.25-2.25h3A2.25 2.25 0 0 1 15.75 6v1.5m-12 3h16.5m-16.5 0v6.75A2.25 2.25 0 0 0 6 19.5h12a2.25 2.25 0 0 0 2.25-2.25V10.5m-16.5 0V9A2.25 2.25 0 0 1 6 6.75h12A2.25 2.25 0 0 1 20.25 9v1.5" /></svg>',
        'calendar' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-6 w-6"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3.75v3m10.5-3v3M4.5 9.75h15m-16.5 1.5v7.5A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75v-7.5A2.25 2.25 0 0 0 18.75 9H5.25A2.25 2.25 0 0 0 3 11.25Z" /></svg>',
        'chart-bar' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-6 w-6"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 20.25v-6.75m4.5 6.75V9.75m4.5 10.5V5.25m-10.5 15h12.75" /></svg>',
        'check-circle' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-6 w-6"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m6 2.25A9 9 0 1 1 3 12a9 9 0 0 1 18 0Z" /></svg>',
        'cloud' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-6 w-6"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15a4.5 4.5 0 0 0 4.5 4.5H17.25a4.5 4.5 0 0 0 .696-8.946A6 6 0 0 0 6.18 8.25 4.5 4.5 0 0 0 2.25 15Z" /></svg>',
        'cog' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-6 w-6"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 3h1.5l.492 2.217a7.47 7.47 0 0 1 1.794.744l1.95-1.263 1.061 1.06-1.263 1.95c.298.565.548 1.164.744 1.794L21 11.25v1.5l-2.217.492a7.474 7.474 0 0 1-.744 1.794l1.263 1.95-1.06 1.061-1.95-1.263a7.47 7.47 0 0 1-1.794.744L12.75 21h-1.5l-.492-2.217a7.47 7.47 0 0 1-1.794-.744l-1.95 1.263-1.061-1.06 1.263-1.95a7.47 7.47 0 0 1-.744-1.794L3 12.75v-1.5l2.217-.492c.196-.63.446-1.229.744-1.794l-1.263-1.95 1.06-1.061 1.95 1.263a7.47 7.47 0 0 1 1.794-.744L11.25 3ZM12 15.75A3.75 3.75 0 1 0 12 8.25a3.75 3.75 0 0 0 0 7.5Z" /></svg>',
        'document-text' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-6 w-6"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-8.25A2.25 2.25 0 0 0 17.25 3.75H8.25A2.25 2.25 0 0 0 6 6v12a2.25 2.25 0 0 0 2.25 2.25h7.5m3.75-6-3.75 3.75m0 0L12 14.25m3.75 3.75V10.5M9.75 8.25h5.25" /></svg>',
        'envelope' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-6 w-6"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 7.5v9A2.25 2.25 0 0 1 19.5 18.75h-15A2.25 2.25 0 0 1 2.25 16.5v-9m19.5 0A2.25 2.25 0 0 0 19.5 5.25h-15A2.25 2.25 0 0 0 2.25 7.5m19.5 0-8.69 5.214a2.25 2.25 0 0 1-2.12 0L2.25 7.5" /></svg>',
        'folder' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-6 w-6"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 7.5A2.25 2.25 0 0 1 4.5 5.25h4.19a2.25 2.25 0 0 1 1.59.659l1.31 1.31a2.25 2.25 0 0 0 1.59.659H19.5A2.25 2.25 0 0 1 21.75 10.5v7.5A2.25 2.25 0 0 1 19.5 20.25h-15A2.25 2.25 0 0 1 2.25 18V7.5Z" /></svg>',
        'globe' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-6 w-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3.75a8.25 8.25 0 1 0 0 16.5m0-16.5c2.071 0 3.75 3.694 3.75 8.25S14.071 20.25 12 20.25m0-16.5c-2.071 0-3.75 3.694-3.75 8.25S9.929 20.25 12 20.25m-7.5-8.25h15" /></svg>',
        'heart' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-6 w-6"><path stroke-linecap="round" stroke-linejoin="round" d="m21.435 8.988-.528 5.079a7.5 7.5 0 0 1-1.514 3.633A7.5 7.5 0 0 1 12 20.25a7.5 7.5 0 0 1-7.393-5.55 7.5 7.5 0 0 1-1.514-3.633l-.528-5.08A2.25 2.25 0 0 1 4.804 3.75c1.029 0 2.003.486 2.63 1.312L12 11.25l4.566-6.188A3.281 3.281 0 0 1 19.196 3.75a2.25 2.25 0 0 1 2.239 2.238Z" /></svg>',
        'home' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-6 w-6"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955a1.125 1.125 0 0 1 1.592 0L21.75 12m-2.25-2.25V19.5A2.25 2.25 0 0 1 17.25 21.75h-10.5A2.25 2.25 0 0 1 4.5 19.5V9.75" /></svg>',
        'light-bulb' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-6 w-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-2.25m0 0a5.25 5.25 0 1 0-5.25-5.25c0 1.52.647 2.888 1.68 3.847.61.566 1.07 1.27 1.32 2.053h4.5c.25-.782.71-1.487 1.32-2.053A5.23 5.23 0 0 0 17.25 10.5 5.25 5.25 0 0 0 12 15.75Zm-2.25 4.5h4.5" /></svg>',
        'link' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-6 w-6"><path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 0 1 6.364 6.364l-3.182 3.182a4.5 4.5 0 0 1-6.364-6.364m3.182-3.182-3.182 3.182m0 0a4.5 4.5 0 0 1-6.364-6.364l3.182-3.182a4.5 4.5 0 0 1 6.364 6.364" /></svg>',
        'lock-closed' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-6 w-6"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V7.875a4.5 4.5 0 1 0-9 0V10.5m-.75 0h10.5A2.25 2.25 0 0 1 19.5 12.75v5.25a2.25 2.25 0 0 1-2.25 2.25H6.75A2.25 2.25 0 0 1 4.5 18V12.75A2.25 2.25 0 0 1 6.75 10.5Z" /></svg>',
        'magnifying-glass' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-6 w-6"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35m0 0a7.125 7.125 0 1 0-10.08 0 7.125 7.125 0 0 0 10.08 0Z" /></svg>',
        'rocket-launch' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-6 w-6"><path stroke-linecap="round" stroke-linejoin="round" d="M15.59 14.37a6 6 0 0 0 4.84-5.2 12.05 12.05 0 0 0-6.09-5.63 12.03 12.03 0 0 0-5.63 6.09 6 6 0 0 0 5.2 4.84m0 0a6 6 0 0 1-1.2 2.35l-.88 1.17a1.5 1.5 0 0 1-2.34.13l-1.1-1.1a1.5 1.5 0 0 1 .13-2.34l1.17-.88a6 6 0 0 1 2.35-1.2m1.87 1.87 1.17.88a1.5 1.5 0 0 1 .13 2.34l-1.1 1.1a1.5 1.5 0 0 1-2.34-.13l-.88-1.17a6 6 0 0 1-1.2-2.35m-3.25 4.88 2.25-2.25m7.5-7.5L18 6" /></svg>',
        'server' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-6 w-6"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 6.75h15a1.5 1.5 0 0 1 1.5 1.5v3a1.5 1.5 0 0 1-1.5 1.5h-15A1.5 1.5 0 0 1 3 11.25v-3a1.5 1.5 0 0 1 1.5-1.5Zm0 7.5h15a1.5 1.5 0 0 1 1.5 1.5v3a1.5 1.5 0 0 1-1.5 1.5h-15A1.5 1.5 0 0 1 3 18.75v-3a1.5 1.5 0 0 1 1.5-1.5ZM7.5 9.75h.008v.008H7.5V9.75Zm0 7.5h.008v.008H7.5v-.008Z" /></svg>',
        'shield-check' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-6 w-6"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m6-3.75-8.25-3-8.25 3v5.25c0 5.25 3.75 8.625 8.25 9.75 4.5-1.125 8.25-4.5 8.25-9.75V9Z" /></svg>',
    ];

    public function getFunctions(): array
    {
        return [
            new TwigFunction('app_icon', [$this, 'renderIcon'], ['is_safe' => ['html']]),
        ];
    }

    public function renderIcon(string $name): string
    {
        return self::ICONS[$name] ?? '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-6 w-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>';
    }

    /**
     * @return list<string>
     */
    public static function availableIcons(): array
    {
        return array_keys(self::ICONS);
    }
}
