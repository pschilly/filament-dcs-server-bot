@php
    $icons = [
        1 => '<span class="text-3xl">&#x1F947;</span>',
        2 => '<span class="text-2xl">&#x1F948;</span>',
        3 => '<span class="text-xl">&#x1F949;</span>',
    ];
    $rowNumber = ($getState() ?? 0);
@endphp
<span class="font-bold">
    {!! $icons[$rowNumber] ?? $rowNumber !!}
</span>