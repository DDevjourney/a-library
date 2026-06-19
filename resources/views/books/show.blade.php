<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-4">
            <h2 class="text-xs uppercase tracking-[0.2em] font-medium text-zinc-500 dark:text-zinc-400 truncate">
                {{ $book->title }}
            </h2>
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('books.edit', $book) }}" class="px-4 py-2 text-xs uppercase tracking-widest font-medium border border-zinc-300 dark:border-zinc-700 text-zinc-600 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition">
                    Editar
                </a>
                <form method="POST" action="{{ route('books.destroy', $book) }}" onsubmit="return confirm('¿Eliminar este libro?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 text-xs uppercase tracking-widest font-medium border border-rose-300 dark:border-rose-900 text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950 transition">
                        Eliminar
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800">
                <div class="grid grid-cols-1 md:grid-cols-3">
                    <!-- Cover -->
                    <div class="md:border-r border-zinc-200 dark:border-zinc-800">
                        @if($book->cover_image)
                            <img src="{{ $book->cover_image }}" alt="{{ $book->title }}" class="w-full aspect-[3/4] object-cover">
                        @else
                            <div class="w-full aspect-[3/4] bg-zinc-50 dark:bg-zinc-800 flex items-center justify-center">
                                <span class="text-[10px] uppercase tracking-widest text-zinc-400">Sin portada</span>
                            </div>
                        @endif
                    </div>

                    <!-- Details -->
                    <div class="md:col-span-2 p-6 sm:p-8">
                        <h1 class="text-2xl font-light text-zinc-900 dark:text-zinc-100">{{ $book->title }}</h1>
                        <p class="text-sm text-zinc-500 mt-1">{{ $book->author }}</p>

                        <div class="mt-4 flex items-center gap-3">
                            <x-status-badge :status="$book->status" />
                            @if($book->rating)
                                <span class="text-sm tabular-nums text-zinc-600 dark:text-zinc-300">
                                    {{ str_repeat('★', $book->rating) }}<span class="text-zinc-300 dark:text-zinc-600">{{ str_repeat('★', 5 - $book->rating) }}</span>
                                </span>
                            @endif
                        </div>

                        <dl class="mt-6 grid grid-cols-2 gap-x-6 gap-y-4 border-t border-zinc-100 dark:border-zinc-800 pt-6">
                            @if($book->published_year)
                                <div>
                                    <dt class="text-[10px] uppercase tracking-widest text-zinc-500">Año</dt>
                                    <dd class="text-sm text-zinc-800 dark:text-zinc-200 mt-0.5 tabular-nums">{{ $book->published_year }}</dd>
                                </div>
                            @endif
                            @if($book->started_at)
                                <div>
                                    <dt class="text-[10px] uppercase tracking-widest text-zinc-500">Inicio</dt>
                                    <dd class="text-sm text-zinc-800 dark:text-zinc-200 mt-0.5 tabular-nums">{{ $book->started_at->format('d/m/Y') }}</dd>
                                </div>
                            @endif
                            @if($book->finished_at)
                                <div>
                                    <dt class="text-[10px] uppercase tracking-widest text-zinc-500">Fin</dt>
                                    <dd class="text-sm text-zinc-800 dark:text-zinc-200 mt-0.5 tabular-nums">{{ $book->finished_at->format('d/m/Y') }}</dd>
                                </div>
                            @endif
                        </dl>

                        @if($book->genres->count())
                            <div class="mt-6">
                                <span class="text-[10px] uppercase tracking-widest text-zinc-500">Géneros</span>
                                <div class="flex flex-wrap gap-2 mt-2">
                                    @foreach($book->genres as $genre)
                                        <a href="{{ route('genres.show', $genre) }}" class="border border-zinc-300 dark:border-zinc-700 px-2 py-0.5 text-[10px] uppercase tracking-widest text-zinc-600 dark:text-zinc-300 hover:border-zinc-900 dark:hover:border-zinc-100">
                                            {{ $genre->name }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                @if($book->review)
                    <div class="border-t border-zinc-200 dark:border-zinc-800 p-6 sm:p-8">
                        <span class="text-[10px] uppercase tracking-widest text-zinc-500">Reseña</span>
                        <p class="mt-3 text-sm leading-relaxed text-zinc-700 dark:text-zinc-300 whitespace-pre-line">{{ $book->review }}</p>
                    </div>
                @endif
            </div>

            <div class="mt-6">
                <a href="{{ route('books.index') }}" class="text-[10px] uppercase tracking-widest text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100">← Volver a libros</a>
            </div>
        </div>
    </div>
</x-app-layout>
