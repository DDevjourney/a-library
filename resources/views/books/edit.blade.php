<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xs uppercase tracking-[0.2em] font-medium text-zinc-500 dark:text-zinc-400">
            {{ __('Editar libro') }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 sm:p-8">
                <form method="POST" action="{{ route('books.update', $book) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    @include('books.partials.form', [
                        'book' => $book,
                        'submitLabel' => 'Actualizar libro',
                        'cancelUrl' => route('books.show', $book),
                    ])
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
