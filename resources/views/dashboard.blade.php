<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xs uppercase tracking-[0.2em] font-medium text-zinc-500 dark:text-zinc-400">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            <!-- Key metrics -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-px bg-zinc-200 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-800">
                <div class="bg-white dark:bg-zinc-900 p-6">
                    <div class="text-[10px] uppercase tracking-widest text-zinc-500">Total de libros</div>
                    <div class="mt-3 text-4xl font-light tabular-nums text-zinc-900 dark:text-zinc-100">{{ $totalBooks }}</div>
                </div>
                <div class="bg-white dark:bg-zinc-900 p-6">
                    <div class="text-[10px] uppercase tracking-widest text-zinc-500">Leídos este año</div>
                    <div class="mt-3 text-4xl font-light tabular-nums text-zinc-900 dark:text-zinc-100">{{ $booksReadThisYear }}</div>
                </div>
                <div class="bg-white dark:bg-zinc-900 p-6">
                    <div class="text-[10px] uppercase tracking-widest text-zinc-500">Calificación media</div>
                    <div class="mt-3 text-4xl font-light tabular-nums text-zinc-900 dark:text-zinc-100">
                        {{ $averageRating > 0 ? number_format($averageRating, 1) : '—' }}
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Status distribution -->
                <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6">
                    <h3 class="text-[10px] uppercase tracking-widest text-zinc-500 mb-5">Distribución por estado</h3>
                    @php
                        $statusMeta = [
                            'por_leer'   => ['Por leer', 'bg-zinc-400'],
                            'leyendo'    => ['Leyendo', 'bg-blue-500'],
                            'leido'      => ['Leído', 'bg-emerald-500'],
                            'abandonado' => ['Abandonado', 'bg-rose-500'],
                        ];
                        $statusTotal = max(array_sum($statusDistribution), 1);
                    @endphp
                    <div class="space-y-4">
                        @foreach($statusMeta as $key => [$label, $dot])
                            <div>
                                <div class="flex items-center justify-between text-xs mb-1.5">
                                    <span class="flex items-center gap-2 uppercase tracking-widest text-zinc-600 dark:text-zinc-300">
                                        <span class="w-1.5 h-1.5 {{ $dot }}"></span>{{ $label }}
                                    </span>
                                    <span class="tabular-nums text-zinc-500">{{ $statusDistribution[$key] }}</span>
                                </div>
                                <div class="h-1 bg-zinc-100 dark:bg-zinc-800">
                                    <div class="h-1 {{ $dot }}" style="width: {{ ($statusDistribution[$key] / $statusTotal) * 100 }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Most read genres -->
                <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6">
                    <h3 class="text-[10px] uppercase tracking-widest text-zinc-500 mb-5">Géneros más leídos</h3>
                    @if($mostReadGenres->count())
                        @php $genreMax = max($mostReadGenres->max('total'), 1); @endphp
                        <div class="space-y-4">
                            @foreach($mostReadGenres as $genre)
                                <div>
                                    <div class="flex items-center justify-between text-xs mb-1.5">
                                        <span class="uppercase tracking-widest text-zinc-600 dark:text-zinc-300">{{ $genre->name }}</span>
                                        <span class="tabular-nums text-zinc-500">{{ $genre->total }}</span>
                                    </div>
                                    <div class="h-1 bg-zinc-100 dark:bg-zinc-800">
                                        <div class="h-1 bg-zinc-900 dark:bg-zinc-100" style="width: {{ ($genre->total / $genreMax) * 100 }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-zinc-500">Aún no has marcado libros como leídos.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
