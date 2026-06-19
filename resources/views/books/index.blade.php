<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xs uppercase tracking-[0.2em] font-medium text-zinc-500 dark:text-zinc-400">
                {{ __('Mis libros') }}
            </h2>
            <a href="{{ route('books.create') }}"
               class="inline-flex items-center gap-2 bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-900 px-4 py-2 text-xs uppercase tracking-widest font-medium hover:bg-zinc-700 dark:hover:bg-white transition">
                + Añadir libro
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Filters -->
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-5 mb-8">
                <form method="GET" action="{{ route('books.index') }}" class="grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                    <div class="md:col-span-4">
                        <label class="block text-[10px] uppercase tracking-widest text-zinc-500 mb-1.5">Buscar</label>
                        <input type="text" name="search" placeholder="Título o autor" value="{{ request('search') }}"
                            class="w-full border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm focus:border-zinc-900 dark:focus:border-zinc-100 focus:ring-0">
                    </div>
                    <div class="md:col-span-3">
                        <label class="block text-[10px] uppercase tracking-widest text-zinc-500 mb-1.5">Estado</label>
                        <select name="status" class="w-full border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm focus:border-zinc-900 dark:focus:border-zinc-100 focus:ring-0">
                            <option value="">Todos</option>
                            <option value="por_leer" @selected(request('status') === 'por_leer')>Por leer</option>
                            <option value="leyendo" @selected(request('status') === 'leyendo')>Leyendo</option>
                            <option value="leido" @selected(request('status') === 'leido')>Leído</option>
                            <option value="abandonado" @selected(request('status') === 'abandonado')>Abandonado</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-[10px] uppercase tracking-widest text-zinc-500 mb-1.5">Género</label>
                        <select name="genre" class="w-full border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm focus:border-zinc-900 dark:focus:border-zinc-100 focus:ring-0">
                            <option value="">Todos</option>
                            @foreach($genres as $genre)
                                <option value="{{ $genre->id }}" @selected(request('genre') == $genre->id)>{{ $genre->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-[10px] uppercase tracking-widest text-zinc-500 mb-1.5">Orden</label>
                        <select name="sort" class="w-full border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm focus:border-zinc-900 dark:focus:border-zinc-100 focus:ring-0">
                            <option value="created_at" @selected(request('sort') === 'created_at' || !request('sort'))>Recientes</option>
                            <option value="title" @selected(request('sort') === 'title')>Título</option>
                            <option value="author" @selected(request('sort') === 'author')>Autor</option>
                            <option value="rating" @selected(request('sort') === 'rating')>Calificación</option>
                        </select>
                    </div>
                    <div class="md:col-span-1 flex gap-2">
                        <button type="submit" class="w-full bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-900 px-3 py-2 text-xs uppercase tracking-widest font-medium hover:bg-zinc-700 dark:hover:bg-white transition">
                            Ir
                        </button>
                    </div>
                </form>
                @if(request()->hasAny(['search', 'status', 'genre', 'sort']))
                    <div class="mt-3">
                        <a href="{{ route('books.index') }}" class="text-[10px] uppercase tracking-widest text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100">Limpiar filtros</a>
                    </div>
                @endif
            </div>

            @if($books->count())
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-px bg-zinc-200 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-800">
                    @foreach($books as $book)
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
                                <div class="mt-3 flex items-center justify-between">
                                    <x-status-badge :status="$book->status" />
                                    @if($book->rating)
                                        <span class="text-xs tabular-nums text-zinc-500">★ {{ $book->rating }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $books->withQueryString()->links() }}
                </div>
            @else
                <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-12 text-center">
                    <p class="text-sm text-zinc-500">No hay libros que coincidan.
                        <a href="{{ route('books.create') }}" class="text-zinc-900 dark:text-zinc-100 underline">Añade uno</a>
                    </p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
