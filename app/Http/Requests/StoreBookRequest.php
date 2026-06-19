<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreBookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'published_year' => 'nullable|integer|min:1000|max:' . date('Y'),
            'status' => 'required|in:por_leer,leyendo,leido,abandonado',
            'rating' => 'nullable|integer|min:1|max:5',
            'started_at' => 'nullable|date',
            'finished_at' => 'nullable|date|after_or_equal:started_at',
            'review' => 'nullable|string',
            'genres' => 'nullable|array',
            'genres.*' => 'integer|exists:genres,id',
            'cover' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'cover_url' => 'nullable|url|max:2048',
        ];
    }
}
