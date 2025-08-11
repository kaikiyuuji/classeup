@props(['active' => false, 'icon' => 'home'])

@php
$classes = ($active ?? false)
            ? 'flex items-center px-3 py-2 text-sm font-medium text-blue-600 bg-blue-50 rounded-md border-l-4 border-blue-600'
            : 'flex items-center px-3 py-2 text-sm font-medium text-gray-600 rounded-md hover:text-gray-900 hover:bg-gray-50 transition-colors duration-150';

// Mapeamento de ícones para componentes Heroicon
$iconMap = [
    'home' => 'heroicon-o-home',
    'user' => 'heroicon-o-user',
    'users' => 'heroicon-o-users',
    'user-group' => 'heroicon-o-user-group',
    'academic-cap' => 'heroicon-o-academic-cap',
    'book-open' => 'heroicon-o-book-open',
    'clipboard-document-list' => 'heroicon-o-clipboard-document-list',
        'document-text' => 'heroicon-o-document-text',
        'document' => 'heroicon-o-document',
        'calendar' => 'heroicon-o-calendar',
        'cog-6-tooth' => 'heroicon-o-cog',
        'arrow-right-on-rectangle' => 'heroicon-o-arrow-right-on-rectangle',
];

$iconComponent = $iconMap[$icon] ?? 'heroicon-o-home';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    <x-dynamic-component :component="$iconComponent" class="w-5 h-5 mr-3" />
    {{ $slot }}
</a>