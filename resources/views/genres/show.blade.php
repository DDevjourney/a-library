<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center gap-4">
            <h2 class="text-xs uppercase tracking-[0.2em] font-medium text-zinc-500 dark:text-zinc-400 truncate">
                {{ $genre->name }}
            </h2>
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('genres.edit', $genre) }}" class="px-4 py-2 text-xs uppercase tracking-widest font-medium border border-zinc-300 dark:border-zinc-700 text-zinc-600 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition">
                    Editar
                </a>
                <form method="POST" action="{{ route('genres.destroy', $genre) }}" onsubmit="return confirm('¿Eliminar este género?')">
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
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 mb-8 flex items-center justify-between">
                <div>
                    <span class="text-[10px] uppercase tracking-widest text-zinc-500">Slug</span>
                    <p class="text-sm font-mono text-zinc-800 dark:text-zinc-200 mt-0.5">{{ $genre->slug }}</p>
                </div>
                <div class="text-right">
                    <span class="text-[10px] uppercase tracking-widest text-zinc-500">Libros</span>
                    <p class="text-2xl font-light tabular-nums text-zinc-900 dark:text-zinc-100">{{ $genre->books->count() }}</p>
                </div>
            </div>

            @if($genre->books->count())
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-px bg-zinc-200 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-800">
                    @foreach($genre->books as $book)
                        <div class="bg-white dark:bg-zinc-900 group">
                            <a href="{{ route('books.show', $book) }}" class="block">
                                @if($book->cover_image)
                                    <img src="{{ $book->cover_image }}" alt="{{ $book->title }}" class="w-full aspect-[3/4] object-cover">
                                @else
                                    <div class="w-full aspect-[3/4] bg-zinc-50 dark:bg-zinc-800 flex items-center justify-center border-b border-zinc-100 dark:border-zinc-800">
                                        <span class="text-[10px] uppercase tracking-widest text-zinc-400">Sin portada</span>
                                    </div>
                                @endif
                            </a>
                            <div class="p-4">
                                <a href="{{ route('books.show', $book) }}" class="block">
                                    <h3 class="text-sm font-medium text-zinc-900 dark:text-zinc-100 truncate group-hover:underline">{{ $book->title }}</h3>
                                    <p class="text-xs text-zinc-500 truncate mt-0.5">{{ $book->author }}</p>
                                </a>
                                <div class="mt-3">
                                    <x-status-badge :status="$book->status" />
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-12 text-center">
                    <p class="text-sm text-zinc-500">No hay libros en este género.</p>
                </div>
            @endif

            <div class="mt-6">
                <a href="{{ route('genres.index') }}" class="text-[10px] uppercase tracking-widest text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100">← Volver a géneros</a>
            </div>
        </div>
    </div>
</x-app-layout>
