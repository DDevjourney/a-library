@php
    $book = $book ?? null;
    $inputClass = 'w-full border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm text-zinc-900 dark:text-zinc-100 focus:border-zinc-900 dark:focus:border-zinc-100 focus:ring-0';
    $labelClass = 'block text-[10px] uppercase tracking-widest text-zinc-500 mb-1.5';
@endphp

<div class="space-y-6">
    <!-- Title -->
    <div>
        <label for="title" class="{{ $labelClass }}">Título</label>
        <input id="title" type="text" name="title" value="{{ old('title', $book?->title) }}" required autofocus class="{{ $inputClass }}">
        <x-input-error :messages="$errors->get('title')" class="mt-2" />
    </div>

    <!-- Author -->
    <div>
        <label for="author" class="{{ $labelClass }}">Autor</label>
        <input id="author" type="text" name="author" value="{{ old('author', $book?->author) }}" required class="{{ $inputClass }}">
        <x-input-error :messages="$errors->get('author')" class="mt-2" />
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <!-- Published Year -->
        <div>
            <label for="published_year" class="{{ $labelClass }}">Año de publicación</label>
            <input id="published_year" type="number" name="published_year" value="{{ old('published_year', $book?->published_year) }}" class="{{ $inputClass }}">
            <x-input-error :messages="$errors->get('published_year')" class="mt-2" />
        </div>

        <!-- Status -->
        <div>
            <label for="status" class="{{ $labelClass }}">Estado</label>
            <select id="status" name="status" required class="{{ $inputClass }}">
                <option value="por_leer" @selected(old('status', $book?->status ?? 'por_leer') === 'por_leer')>Por leer</option>
                <option value="leyendo" @selected(old('status', $book?->status) === 'leyendo')>Leyendo</option>
                <option value="leido" @selected(old('status', $book?->status) === 'leido')>Leído</option>
                <option value="abandonado" @selected(old('status', $book?->status) === 'abandonado')>Abandonado</option>
            </select>
            <x-input-error :messages="$errors->get('status')" class="mt-2" />
        </div>
    </div>

    <!-- Cover -->
    <div class="border border-zinc-200 dark:border-zinc-800 p-4 space-y-4">
        <span class="{{ $labelClass }}">Portada</span>
        @if($book?->cover_image)
            <img src="{{ $book->cover_image }}" alt="{{ $book->title }}" class="h-32 w-auto object-cover border border-zinc-200 dark:border-zinc-800">
        @endif
        <div>
            <label for="cover_url" class="{{ $labelClass }}">Desde una URL</label>
            <input id="cover_url" type="url" name="cover_url" placeholder="https://…" value="{{ old('cover_url', $book?->cover_url) }}" class="{{ $inputClass }}">
            <x-input-error :messages="$errors->get('cover_url')" class="mt-2" />
        </div>
        <div>
            <label for="cover" class="{{ $labelClass }}">O subir un archivo</label>
            <input id="cover" type="file" name="cover" accept="image/*" class="block w-full text-sm text-zinc-600 dark:text-zinc-400 file:mr-3 file:border-0 file:bg-zinc-900 dark:file:bg-zinc-100 file:text-white dark:file:text-zinc-900 file:px-3 file:py-2 file:text-xs file:uppercase file:tracking-widest">
            <x-input-error :messages="$errors->get('cover')" class="mt-2" />
            <p class="mt-1.5 text-[10px] text-zinc-400">Si subes un archivo, tiene prioridad sobre la URL.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <!-- Rating -->
        <div>
            <label for="rating" class="{{ $labelClass }}">Calificación (1–5)</label>
            <input id="rating" type="number" name="rating" min="1" max="5" value="{{ old('rating', $book?->rating) }}" class="{{ $inputClass }}">
            <x-input-error :messages="$errors->get('rating')" class="mt-2" />
        </div>

        <!-- Started At -->
        <div>
            <label for="started_at" class="{{ $labelClass }}">Inicio</label>
            <input id="started_at" type="date" name="started_at" value="{{ old('started_at', $book?->started_at?->format('Y-m-d')) }}" class="{{ $inputClass }}">
            <x-input-error :messages="$errors->get('started_at')" class="mt-2" />
        </div>

        <!-- Finished At -->
        <div>
            <label for="finished_at" class="{{ $labelClass }}">Fin</label>
            <input id="finished_at" type="date" name="finished_at" value="{{ old('finished_at', $book?->finished_at?->format('Y-m-d')) }}" class="{{ $inputClass }}">
            <x-input-error :messages="$errors->get('finished_at')" class="mt-2" />
        </div>
    </div>

    <!-- Review -->
    <div>
        <label for="review" class="{{ $labelClass }}">Reseña</label>
        <textarea id="review" name="review" rows="4" class="{{ $inputClass }}">{{ old('review', $book?->review) }}</textarea>
        <x-input-error :messages="$errors->get('review')" class="mt-2" />
    </div>

    <!-- Genres -->
    <div>
        <span class="{{ $labelClass }}">Géneros</span>
        @php($selected = old('genres', $book ? $book->genres->pluck('id')->toArray() : []))
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 mt-1">
            @foreach($genres as $genre)
                <label class="flex items-center gap-2 border border-zinc-200 dark:border-zinc-800 px-3 py-2 text-sm cursor-pointer hover:border-zinc-400 dark:hover:border-zinc-600">
                    <input type="checkbox" name="genres[]" value="{{ $genre->id }}"
                        @checked(in_array($genre->id, $selected))
                        class="border-zinc-400 text-zinc-900 focus:ring-0">
                    <span class="text-zinc-700 dark:text-zinc-300 truncate">{{ $genre->name }}</span>
                </label>
            @endforeach
        </div>
        <x-input-error :messages="$errors->get('genres')" class="mt-2" />
    </div>

    <!-- Actions -->
    <div class="flex items-center gap-3 pt-2">
        <button type="submit" class="inline-flex items-center gap-2 bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-900 px-5 py-2.5 text-xs uppercase tracking-widest font-medium hover:bg-zinc-700 dark:hover:bg-white transition">
            {{ $submitLabel }}
        </button>
        <a href="{{ $cancelUrl }}" class="px-5 py-2.5 text-xs uppercase tracking-widest font-medium border border-zinc-300 dark:border-zinc-700 text-zinc-600 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition">
            Cancelar
        </a>
    </div>
</div>
