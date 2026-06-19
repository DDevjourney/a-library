<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xs uppercase tracking-[0.2em] font-medium text-zinc-500 dark:text-zinc-400">
            {{ __('Editar género') }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 sm:p-8">
                <form method="POST" action="{{ route('genres.update', $genre) }}" class="space-y-6">
                    @csrf
                    @method('PUT')
                    <div>
                        <label for="name" class="block text-[10px] uppercase tracking-widest text-zinc-500 mb-1.5">Nombre</label>
                        <input id="name" type="text" name="name" value="{{ old('name', $genre->name) }}" required autofocus
                            class="w-full border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm text-zinc-900 dark:text-zinc-100 focus:border-zinc-900 dark:focus:border-zinc-100 focus:ring-0">
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>
                    <div class="flex items-center gap-3 pt-2">
                        <button type="submit" class="inline-flex items-center gap-2 bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-900 px-5 py-2.5 text-xs uppercase tracking-widest font-medium hover:bg-zinc-700 dark:hover:bg-white transition">
                            Actualizar género
                        </button>
                        <a href="{{ route('genres.index') }}" class="px-5 py-2.5 text-xs uppercase tracking-widest font-medium border border-zinc-300 dark:border-zinc-700 text-zinc-600 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition">
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
