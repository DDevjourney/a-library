@props(['status'])

@php
    $map = [
        'por_leer'    => ['Por leer', 'bg-zinc-400'],
        'leyendo'     => ['Leyendo', 'bg-blue-500'],
        'leido'       => ['Leído', 'bg-emerald-500'],
        'abandonado'  => ['Abandonado', 'bg-rose-500'],
    ];
    [$label, $dot] = $map[$status] ?? ['—', 'bg-zinc-400'];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 border border-zinc-300 dark:border-zinc-700 px-2 py-0.5 text-[10px] uppercase tracking-widest font-medium text-zinc-600 dark:text-zinc-300']) }}>
    <span class="w-1.5 h-1.5 {{ $dot }}"></span>
    {{ $label }}
</span>
