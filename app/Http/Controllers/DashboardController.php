<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $user = auth()->user();
        $books = $user->books();

        $totalBooks = $books->count();
        $booksReadThisYear = $books->whereYear('finished_at', Carbon::now()->year)->count();

        $statusDistribution = [
            'por_leer' => $books->where('status', 'por_leer')->count(),
            'leyendo' => $books->where('status', 'leyendo')->count(),
            'leido' => $books->where('status', 'leido')->count(),
            'abandonado' => $books->where('status', 'abandonado')->count(),
        ];

        $averageRating = $books->whereNotNull('rating')->avg('rating') ?? 0;

        // Géneros más leídos: cuenta libros marcados como "leído" por género.
        $mostReadGenres = Genre::query()
            ->select('genres.name', DB::raw('count(books.id) as total'))
            ->join('book_genre', 'genres.id', '=', 'book_genre.genre_id')
            ->join('books', 'books.id', '=', 'book_genre.book_id')
            ->where('books.user_id', $user->id)
            ->where('books.status', 'leido')
            ->groupBy('genres.id', 'genres.name')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'totalBooks',
            'booksReadThisYear',
            'statusDistribution',
            'averageRating',
            'mostReadGenres'
        ));
    }
}
