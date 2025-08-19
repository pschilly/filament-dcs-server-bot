@php
    $icons = [
        1 => '<span style="color:gold;">&#x1F947;</span>',
        2 => '<span style="color:silver;">&#x1F948;</span>',
        3 => '<span style="color:#cd7f32;">&#x1F949;</span>',
    ];
    $rowNumber = ($getState() ?? 0);
@endphp
<span>
    {!! $icons[$rowNumber] ?? $rowNumber !!}
</span>