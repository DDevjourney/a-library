<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use App\Http\Requests\StoreGenreRequest;
use App\Http\Requests\UpdateGenreRequest;

class GenreController extends Controller
{

    public function index()
    {
        $genres = Genre::paginate(20);
        return view('genres.index', compact('genres'));
    }

    public function create()
    {
        return view('genres.create');
    }

    public function store(StoreGenreRequest $request)
    {
        $data = $request->validated();
        $data['slug'] = \Illuminate\Support\Str::slug($data['name']);
        Genre::create($data);
        return redirect()->route('genres.index')->with('success', 'Género creado exitosamente.');
    }

    public function show(Genre $genre)
    {
        $genre->load('books');
        return view('genres.show', compact('genre'));
    }

    public function edit(Genre $genre)
    {
        return view('genres.edit', compact('genre'));
    }

    public function update(UpdateGenreRequest $request, Genre $genre)
    {
        $data = $request->validated();
        $data['slug'] = \Illuminate\Support\Str::slug($data['name']);
        $genre->update($data);
        return redirect()->route('genres.index')->with('success', 'Género actualizado exitosamente.');
    }

    public function destroy(Genre $genre)
    {
        $genre->delete();
        return redirect()->route('genres.index')->with('success', 'Género eliminado exitosamente.');
    }
}
