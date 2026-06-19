<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xs uppercase tracking-[0.2em] font-medium text-zinc-500 dark:text-zinc-400">
                {{ __('Géneros') }}
            </h2>
            <a href="{{ route('genres.create') }}"
               class="inline-flex items-center gap-2 bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-900 px-4 py-2 text-xs uppercase tracking-widest font-medium hover:bg-zinc-700 dark:hover:bg-white transition">
                + Nuevo género
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            @if($genres->count())
                <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-zinc-200 dark:border-zinc-800">
                                <th class="px-5 py-3 text-left text-[10px] uppercase tracking-widest text-zinc-500 font-medium">Nombre</th>
                                <th class="px-5 py-3 text-left text-[10px] uppercase tracking-widest text-zinc-500 font-medium">Slug</th>
                                <th class="px-5 py-3 text-left text-[10px] uppercase tracking-widest text-zinc-500 font-medium">Libros</th>
                                <th class="px-5 py-3 text-right text-[10px] uppercase tracking-widest text-zinc-500 font-medium">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                            @foreach($genres as $genre)
                                <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                                    <td class="px-5 py-3 font-medium text-zinc-900 dark:text-zinc-100">{{ $genre->name }}</td>
                                    <td class="px-5 py-3 text-zinc-500 font-mono text-xs">{{ $genre->slug }}</td>
                                    <td class="px-5 py-3 text-zinc-500 tabular-nums">{{ $genre->books_count ?? $genre->books()->count() }}</td>
                                    <td class="px-5 py-3 text-right">
                                        <div class="inline-flex items-center gap-3 text-[10px] uppercase tracking-widest">
                                            <a href="{{ route('genres.show', $genre) }}" class="text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100">Ver</a>
                                            <a href="{{ route('genres.edit', $genre) }}" class="text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100">Editar</a>
                                            <form method="POST" action="{{ route('genres.destroy', $genre) }}" class="inline" onsubmit="return confirm('¿Eliminar este género?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-rose-500 hover:text-rose-700">Eliminar</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-8">
                    {{ $genres->links() }}
                </div>
            @else
                <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-12 text-center">
                    <p class="text-sm text-zinc-500">No hay géneros.
                        <a href="{{ route('genres.create') }}" class="text-zinc-900 dark:text-zinc-100 underline">Crea uno</a>
                    </p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
