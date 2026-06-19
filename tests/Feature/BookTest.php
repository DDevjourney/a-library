<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private User $otherUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->otherUser = User::factory()->create();
    }

    public function test_authenticated_user_can_view_their_books(): void
    {
        $book = Book::factory()->for($this->user)->create();

        $response = $this->actingAs($this->user)->get(route('books.index'));

        $response->assertStatus(200);
        $response->assertSeeText($book->title);
    }

    public function test_user_cannot_see_other_users_books_in_index(): void
    {
        $otherBook = Book::factory()->for($this->otherUser)->create();

        $response = $this->actingAs($this->user)->get(route('books.index'));

        $response->assertDontSeeText($otherBook->title);
    }

    public function test_authenticated_user_can_view_create_form(): void
    {
        $response = $this->actingAs($this->user)->get(route('books.create'));

        $response->assertStatus(200);
    }

    public function test_authenticated_user_can_create_a_book(): void
    {
        $data = [
            'title' => 'Test Book',
            'author' => 'Test Author',
            'status' => 'por_leer',
        ];

        $response = $this->actingAs($this->user)->post(route('books.store'), $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('books', [
            'title' => 'Test Book',
            'user_id' => $this->user->id,
        ]);
    }

    public function test_book_creation_requires_title_and_author(): void
    {
        $data = ['status' => 'por_leer'];

        $response = $this->actingAs($this->user)->post(route('books.store'), $data);

        $response->assertSessionHasErrors(['title', 'author']);
    }

    public function test_user_can_view_their_own_book(): void
    {
        $book = Book::factory()->for($this->user)->create();

        $response = $this->actingAs($this->user)->get(route('books.show', $book));

        $response->assertStatus(200);
        $response->assertSeeText($book->title);
    }

    public function test_user_cannot_view_another_users_book(): void
    {
        $otherBook = Book::factory()->for($this->otherUser)->create();

        $response = $this->actingAs($this->user)->get(route('books.show', $otherBook));

        $response->assertStatus(403);
    }

    public function test_user_can_view_edit_form_for_their_book(): void
    {
        $book = Book::factory()->for($this->user)->create();

        $response = $this->actingAs($this->user)->get(route('books.edit', $book));

        $response->assertStatus(200);
    }

    public function test_user_cannot_edit_another_users_book(): void
    {
        $otherBook = Book::factory()->for($this->otherUser)->create();

        $response = $this->actingAs($this->user)->get(route('books.edit', $otherBook));

        $response->assertStatus(403);
    }

    public function test_user_can_update_their_book(): void
    {
        $book = Book::factory()->for($this->user)->create();

        $data = [
            'title' => 'Updated Title',
            'author' => $book->author,
            'status' => 'leyendo',
        ];

        $response = $this->actingAs($this->user)->put(route('books.update', $book), $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'title' => 'Updated Title',
        ]);
    }

    public function test_user_cannot_update_another_users_book(): void
    {
        $otherBook = Book::factory()->for($this->otherUser)->create();

        $data = [
            'title' => 'Hacked Title',
            'author' => $otherBook->author,
            'status' => $otherBook->status,
        ];

        $response = $this->actingAs($this->user)->put(route('books.update', $otherBook), $data);

        $response->assertStatus(403);
    }

    public function test_user_can_delete_their_book(): void
    {
        $book = Book::factory()->for($this->user)->create();
        $bookId = $book->id;

        $response = $this->actingAs($this->user)->delete(route('books.destroy', $book));

        $response->assertRedirect();
        $this->assertDatabaseMissing('books', ['id' => $bookId]);
    }

    public function test_user_cannot_delete_another_users_book(): void
    {
        $otherBook = Book::factory()->for($this->otherUser)->create();

        $response = $this->actingAs($this->user)->delete(route('books.destroy', $otherBook));

        $response->assertStatus(403);
    }

    public function test_unauthenticated_user_cannot_create_book(): void
    {
        $response = $this->get(route('books.create'));

        $response->assertRedirect(route('login'));
    }

    public function test_user_can_filter_books_by_status(): void
    {
        Book::factory()->for($this->user)->create(['status' => 'por_leer']);
        Book::factory()->for($this->user)->create(['status' => 'leido']);

        $response = $this->actingAs($this->user)->get(route('books.index', ['status' => 'leido']));

        $response->assertStatus(200);
    }

    public function test_user_can_search_books_by_title(): void
    {
        $book = Book::factory()->for($this->user)->create(['title' => 'Unique Title']);

        $response = $this->actingAs($this->user)->get(route('books.index', ['search' => 'Unique']));

        $response->assertStatus(200);
        $response->assertSeeText('Unique Title');
    }

    public function test_book_can_have_genres(): void
    {
        $genres = Genre::factory(3)->create();

        $data = [
            'title' => 'Test Book',
            'author' => 'Test Author',
            'status' => 'por_leer',
            'genres' => $genres->pluck('id')->toArray(),
        ];

        $this->actingAs($this->user)->post(route('books.store'), $data);

        $book = Book::where('title', 'Test Book')->first();
        $this->assertEquals(3, $book->genres()->count());
    }

    public function test_book_can_have_cover_via_url(): void
    {
        $url = 'https://example.com/cover.jpg';

        $data = [
            'title' => 'Book With Cover URL',
            'author' => 'Test Author',
            'status' => 'por_leer',
            'cover_url' => $url,
        ];

        $this->actingAs($this->user)->post(route('books.store'), $data);

        $book = Book::where('title', 'Book With Cover URL')->first();
        $this->assertEquals($url, $book->cover_url);
        $this->assertEquals($url, $book->cover_image);
    }

    public function test_cover_url_must_be_valid_url(): void
    {
        $data = [
            'title' => 'Invalid Cover',
            'author' => 'Test Author',
            'status' => 'por_leer',
            'cover_url' => 'not-a-url',
        ];

        $response = $this->actingAs($this->user)->post(route('books.store'), $data);

        $response->assertSessionHasErrors('cover_url');
    }
}
