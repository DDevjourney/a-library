<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GenreTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_authenticated_user_can_view_genres_list(): void
    {
        Genre::factory(3)->create();

        $response = $this->actingAs($this->user)->get(route('genres.index'));

        $response->assertStatus(200);
    }

    public function test_authenticated_user_can_view_create_genre_form(): void
    {
        $response = $this->actingAs($this->user)->get(route('genres.create'));

        $response->assertStatus(200);
    }

    public function test_authenticated_user_can_create_a_genre(): void
    {
        $data = ['name' => 'Science Fiction'];

        $response = $this->actingAs($this->user)->post(route('genres.store'), $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('genres', [
            'name' => 'Science Fiction',
            'slug' => 'science-fiction',
        ]);
    }

    public function test_genre_creation_requires_name(): void
    {
        $response = $this->actingAs($this->user)->post(route('genres.store'), []);

        $response->assertSessionHasErrors('name');
    }

    public function test_genre_name_must_be_unique(): void
    {
        Genre::factory()->create(['name' => 'Fantasy']);

        $data = ['name' => 'Fantasy'];

        $response = $this->actingAs($this->user)->post(route('genres.store'), $data);

        $response->assertSessionHasErrors('name');
    }

    public function test_authenticated_user_can_view_genre(): void
    {
        $genre = Genre::factory()->create();

        $response = $this->actingAs($this->user)->get(route('genres.show', $genre));

        $response->assertStatus(200);
        $response->assertSeeText($genre->name);
    }

    public function test_authenticated_user_can_view_edit_genre_form(): void
    {
        $genre = Genre::factory()->create();

        $response = $this->actingAs($this->user)->get(route('genres.edit', $genre));

        $response->assertStatus(200);
    }

    public function test_authenticated_user_can_update_a_genre(): void
    {
        $genre = Genre::factory()->create();

        $data = ['name' => 'Updated Genre Name'];

        $response = $this->actingAs($this->user)->put(route('genres.update', $genre), $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('genres', [
            'id' => $genre->id,
            'name' => 'Updated Genre Name',
        ]);
    }

    public function test_authenticated_user_can_delete_a_genre(): void
    {
        $genre = Genre::factory()->create();
        $genreId = $genre->id;

        $response = $this->actingAs($this->user)->delete(route('genres.destroy', $genre));

        $response->assertRedirect();
        $this->assertDatabaseMissing('genres', ['id' => $genreId]);
    }

    public function test_unauthenticated_user_cannot_create_genre(): void
    {
        $response = $this->get(route('genres.create'));

        $response->assertRedirect(route('login'));
    }

    public function test_genre_displays_associated_books(): void
    {
        $genre = Genre::factory()->create();
        $books = Book::factory(3)->create();

        foreach ($books as $book) {
            $book->genres()->attach($genre);
        }

        $response = $this->actingAs($this->user)->get(route('genres.show', $genre));

        $response->assertStatus(200);
    }
}
