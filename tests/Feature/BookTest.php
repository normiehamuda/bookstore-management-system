<?php

use Tests\TestCase;
use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

class BookTest extends TestCase
{
  use RefreshDatabase;

  protected function setUp(): void
  {
    parent::setUp();
    $this->seed();
  }

  public function test_unauthenticated_user_cannot_create_book()
  {
    $bookData = Book::factory()->make()->toArray();

    $response = $this->postJson('/api/books', $bookData);

    $response->assertStatus(403);
  }

  public function test_unauthenticated_user_cannot_update_book()
  {
    $book = Book::factory()->create();
    $updatedBookData = Book::factory()->make()->toArray();

    $response = $this->putJson("/api/books/{$book->id}", $updatedBookData);

    $response->assertStatus(403);
  }

  public function test_unauthenticated_user_cannot_delete_book()
  {
    $book = Book::factory()->create();

    $response = $this->deleteJson("/api/books/{$book->id}");

    $response->assertStatus(403);
  }

  public function test_regular_user_cannot_create_book()
  {
    $user = User::factory()->create(['role_id' => 2]);
    Sanctum::actingAs($user);

    $bookData = Book::factory()->make()->toArray();

    $response = $this->postJson('/api/books', $bookData);

    $response->assertStatus(401);
  }

  public function test_regular_user_cannot_update_book()
  {
    $user = User::factory()->create(['role_id' => 2]);
    Sanctum::actingAs($user);

    $book = Book::factory()->create();
    $updatedBookData = Book::factory()->make()->toArray();

    $response = $this->putJson("/api/books/{$book->id}", $updatedBookData);

    $response->assertStatus(401);
  }

  public function test_regular_user_cannot_delete_book()
  {
    $user = User::factory()->create(['role_id' => 2]);
    Sanctum::actingAs($user);

    $book = Book::factory()->create();

    $response = $this->deleteJson("/api/books/{$book->id}");

    $response->assertStatus(401);
  }

  public function test_admin_user_can_create_book()
  {
    $admin = User::factory()->create(['role_id' => 1]);
    Sanctum::actingAs($admin);

    $bookData = Book::factory()->make()->toArray();

    $response = $this->postJson('/api/books', $bookData);

    $response->assertStatus(201)
      ->assertJsonStructure([
        'book' => [
          'id',
          'title',
          'author',
          'description',
          'price',
          'isbn',
        ],
        'success'
      ]);

    $this->assertDatabaseHas('books', $bookData);
  }

  public function test_admin_user_can_update_book()
  {
    $admin = User::factory()->create(['role_id' => 1]);
    Sanctum::actingAs($admin);

    $book = Book::factory()->create();
    $updatedBookData = Book::factory()->make()->toArray();

    $response = $this->putJson("/api/books/{$book->id}", $updatedBookData);

    $response->assertStatus(200)
      ->assertJsonStructure([
        'book' => [
          'id',
          'title',
          'author',
          'description',
          'price',
          'isbn',
        ],
        'success'
      ]);

    $this->assertDatabaseHas('books', $updatedBookData);
  }

  public function test_admin_user_can_delete_book()
  {
    $admin = User::factory()->create(['role_id' => 1]);
    Sanctum::actingAs($admin);

    $book = Book::factory()->create();

    $response = $this->deleteJson("/api/books/{$book->id}");

    $response->assertStatus(200)
      ->assertJson([
        'success' => 'Book deleted successfully!'
      ]);

    $this->assertDatabaseMissing('books', ['id' => $book->id]);
  }

  public function test_create_book_with_valid_data_succeeds()
  {
    $admin = User::factory()->create(['role_id' => 1]);
    Sanctum::actingAs($admin);

    $bookData = Book::factory()->make()->toArray();

    $response = $this->postJson('/api/books', $bookData);

    $response->assertStatus(201)
      ->assertJsonStructure([
        'book' => [
          'id',
          'title',
          'author',
          'description',
          'price',
          'isbn',
        ],
        'success'
      ]);

    $this->assertDatabaseHas('books', $bookData);
  }

  public function test_create_book_with_invalid_data_fails()
  {
    $admin = User::factory()->create(['role_id' => 1]);
    Sanctum::actingAs($admin);

    $bookData = Book::factory()->make(['title' => null])->toArray();
    $response = $this->postJson('/api/books', $bookData);
    $response->assertStatus(422);

    $bookData = Book::factory()->make(['isbn' => '12345'])->toArray();
    $response = $this->postJson('/api/books', $bookData);
    $response->assertStatus(422);
  }


  public function test_get_all_books_returns_paginated_list()
  {
    Book::factory(15)->create();

    $response = $this->getJson('/api/books');

    $response->assertStatus(200)
      ->assertJsonStructure([
        'data' => [
          '*' => [
            'id',
            'title',
            'author',
            'description',
            'price',
            'isbn',
          ]
        ],
        'meta' => [
          'current_page',
          'last_page',
          'per_page',
          'total',
        ]
      ]);

    $this->assertCount(10, $response->json('data'));
  }

  public function test_get_single_book_returns_book_data()
  {
    $book = Book::factory()->create();

    $response = $this->getJson("/api/books/{$book->id}");

    $response->assertStatus(200)
      ->assertJson($book->toArray());
  }

  public function test_get_non_existing_book_returns_404()
  {
    $response = $this->getJson('/api/books/9999');

    $response->assertStatus(404);
  }


  public function test_update_book_with_valid_data_succeeds()
  {
    $admin = User::factory()->create(['role_id' => 1]);
    Sanctum::actingAs($admin);

    $book = Book::factory()->create();
    $updatedBookData = Book::factory()->make()->toArray();

    $response = $this->putJson("/api/books/{$book->id}", $updatedBookData);

    $response->assertStatus(200)
      ->assertJsonStructure([
        'book' => [
          'id',
          'title',
          'author',
          'description',
          'price',
          'isbn',
        ],
        'success'
      ]);

    $this->assertDatabaseHas('books', $updatedBookData);
  }

  public function test_update_book_with_invalid_data_fails()
  {
    $admin = User::factory()->create(['role_id' => 1]);
    Sanctum::actingAs($admin);

    $book = Book::factory()->create();

    $updatedBookData = Book::factory()->make(['title' => null])->toArray();
    $response = $this->putJson("/api/books/{$book->id}", $updatedBookData);
    $response->assertStatus(422);

    $updatedBookData = Book::factory()->make(['isbn' => '123'])->toArray();
    $response = $this->putJson("/api/books/{$book->id}", $updatedBookData);
    $response->assertStatus(422);

    $updatedBookData = Book::factory()->make(['author' => null])->toArray();
    $response = $this->putJson("/api/books/{$book->id}", $updatedBookData);
    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['author']);

    $updatedBookData = Book::factory()->make(['price' => 'abc'])->toArray();
    $response = $this->putJson("/api/books/{$book->id}", $updatedBookData);
    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['price']);

    $updatedBookData = Book::factory()->make(['price' => -10])->toArray();
    $response = $this->putJson("/api/books/{$book->id}", $updatedBookData);
    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['price']);

    $updatedBookData = Book::factory()->make(['description' => 'Too short'])->toArray();
    $response = $this->putJson("/api/books/{$book->id}", $updatedBookData);
    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['description']);
  }

  public function test_update_non_existing_book_returns_404()
  {
    $admin = User::factory()->create(['role_id' => 1]);
    Sanctum::actingAs($admin);

    $updatedBookData = Book::factory()->make()->toArray();

    $response = $this->putJson('/api/books/9999', $updatedBookData);

    $response->assertStatus(404);
  }

  public function test_delete_existing_book_succeeds()
  {
    $admin = User::factory()->create(['role_id' => 1]);
    Sanctum::actingAs($admin);

    $book = Book::factory()->create();

    $response = $this->deleteJson("/api/books/{$book->id}");

    $response->assertStatus(200)
      ->assertJson([
        'success' => 'Book deleted successfully!'
      ]);

    $this->assertDatabaseMissing('books', ['id' => $book->id]);
  }

  public function test_delete_non_existing_book_returns_404()
  {
    $admin = User::factory()->create(['role_id' => 1]);
    Sanctum::actingAs($admin);

    $response = $this->deleteJson('/api/books/9999');

    $response->assertStatus(404);
  }

  public function test_search_books_by_title_returns_matching_books()
  {
    Book::factory()->create(['title' => 'Laravel for Beginners']);
    Book::factory()->create(['title' => 'Advanced Laravel']);
    Book::factory()->create(['title' => 'PHP Basics']);

    $response = $this->getJson('/api/books/search?q=Laravel');

    $response->assertStatus(200)
      ->assertJsonCount(2, 'data');
  }

  public function test_search_books_by_author_returns_matching_books()
  {
    Book::factory()->create(['author' => 'Nerd Nerdanton']);
    Book::factory()->create(['author' => 'King Nerd']);
    Book::factory()->create(['author' => 'Basic Guy']);

    $response = $this->getJson('/api/books/search?q=Nerd');

    $response->assertStatus(200)
      ->assertJsonCount(2, 'data');
  }

  public function test_search_books_by_isbn_returns_matching_books()
  {
    Book::factory()->create(['isbn' => '9780134757721']);
    Book::factory()->create(['isbn' => '9780134757722']);
    Book::factory()->create(['isbn' => '9780134757723']);

    $response = $this->getJson('/api/books/search?q=9780134757721');

    $response->assertStatus(200)
      ->assertJsonCount(1, 'data');
  }


  public function test_search_with_no_matching_books_returns_empty_result()
  {
    Book::factory()->create(['title' => 'Laravel for Beginners']);

    $response = $this->getJson('/api/books/search?q=Python');

    $response->assertStatus(404);
  }



  public function test_pagination_returns_correct_number_of_books_per_page()
  {
    Book::factory(15)->create();

    $response = $this->getJson('/api/books?per_page=5');

    $response->assertStatus(200);
    $response->assertJsonCount(5, 'data');
  }

  public function test_pagination_returns_correct_meta_data()
  {
    Book::factory(50)->create();

    $response = $this->getJson('/api/books?per_page=10');

    $response->assertStatus(200);
    $response->assertJsonPath('meta.current_page.0', 1);
    $response->assertJsonPath('meta.last_page.0', 5);
    $response->assertJsonPath('meta.per_page.0', 10);
    $response->assertJsonPath('meta.total.0', 50);
  }

  public function test_title_is_required_when_creating_book()
  {
    $admin = User::factory()->create(['role_id' => 1]);
    Sanctum::actingAs($admin);

    $bookData = Book::factory()->make(['title' => null])->toArray();

    $response = $this->postJson('/api/books', $bookData);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['title']);
  }

  public function test_isbn_must_be_13_characters_long()
  {
    $admin = User::factory()->create(['role_id' => 1]);
    Sanctum::actingAs($admin);

    $bookData = Book::factory()->make(['isbn' => '12345'])->toArray();

    $response = $this->postJson('/api/books', $bookData);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors(['isbn']);
  }
}