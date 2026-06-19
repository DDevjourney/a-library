<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Genre;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $query = auth()->user()->books();

        if ($search = $request->input('search')) {
            $query->search($search);
        }

        if ($status = $request->input('status')) {
            $query->status($status);
        }

        if ($genreId = $request->input('genre')) {
            $query->whereHas('genres', fn ($q) => $q->where('genres.id', $genreId));
        }

        $sortBy = $request->input('sort', 'created_at');
        $sortDir = $request->input('direction', 'desc');
        $query->orderBy($sortBy, $sortDir);

        $books = $query->with('genres')->paginate(12);
        $genres = Genre::all();

        return view('books.index', compact('books', 'genres'));
    }

    public function create()
    {
        $genres = Genre::all();
        return view('books.create', compact('genres'));
    }

    public function store(StoreBookRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('cover')) {
            $data['cover_path'] = $request->file('cover')->store('covers', 'public');
            $data['cover_url'] = null;
        }

        $book = auth()->user()->books()->create($data);

        if ($request->input('genres')) {
            $book->genres()->sync($request->input('genres'));
        }

        return redirect()->route('books.show', $book)->with('success', 'Libro creado exitosamente.');
    }

    public function show(Book $book)
    {
        Gate::authorize('view', $book);
        return view('books.show', compact('book'));
    }

    public function edit(Book $book)
    {
        Gate::authorize('update', $book);
        $genres = Genre::all();
        return view('books.edit', compact('book', 'genres'));
    }

    public function update(UpdateBookRequest $request, Book $book)
    {
        Gate::authorize('update', $book);
        $data = $request->validated();

        if ($request->hasFile('cover')) {
            if ($book->cover_path) {
                Storage::disk('public')->delete($book->cover_path);
            }
            $data['cover_path'] = $request->file('cover')->store('covers', 'public');
            $data['cover_url'] = null;
        }

        $book->update($data);

        if ($request->input('genres')) {
            $book->genres()->sync($request->input('genres'));
        }

        return redirect()->route('books.show', $book)->with('success', 'Libro actualizado exitosamente.');
    }

    public function destroy(Book $book)
    {
        Gate::authorize('delete', $book);
        if ($book->cover_path) {
            Storage::disk('public')->delete($book->cover_path);
        }

        $book->delete();

        return redirect()->route('books.index')->with('success', 'Libro eliminado exitosamente.');
    }
}
