@props([
    'field' => '',
    'currentSort' => '',
    'currentDirection' => 'asc',
    'label' => '',
    'class' => 'px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider'
])

@php
    $isActive = $currentSort === $field;
    $nextDirection = $isActive && $currentDirection === 'asc' ? 'desc' : 'asc';
    $url = request()->fullUrlWithQuery([
        'sort' => $field,
        'direction' => $nextDirection
    ]);
@endphp

<th scope="col" class="{{ $class }}">
    <a href="{{ $url }}" class="group inline-flex items-center hover:text-gray-900 transition-colors duration-150">
        <span>{{ $label }}</span>
        
        <!-- Ícones de ordenação -->
        <span class="ml-2 flex-none rounded">
            @if($isActive)
                @if($currentDirection === 'asc')
                    <!-- Seta para cima (crescente) -->
                    <svg class="w-4 h-4 text-gray-900" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd" />
                    </svg>
                @else
                    <!-- Seta para baixo (decrescente) -->
                    <svg class="w-4 h-4 text-gray-900" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                @endif
            @else
                <!-- Ícone neutro (não ordenado) -->
                <svg class="w-4 h-4 text-gray-400 group-hover:text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M5 12a1 1 0 102 0V6.414l1.293 1.293a1 1 0 001.414-1.414l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L5 6.414V12zM15 8a1 1 0 10-2 0v5.586l-1.293-1.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L15 13.586V8z" />
                </svg>
            @endif
        </span>
    </a>
</th>