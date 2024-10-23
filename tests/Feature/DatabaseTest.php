<?php

use Tests\TestCase;
use App\Models\Book;
use App\Models\User;
use App\Models\Role;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator as FacadesValidator;
use Illuminate\Validation\ValidationException;

class DatabaseTest extends TestCase
{
  use RefreshDatabase; // Reset the database after each test

  protected function setUp(): void
  {
    parent::setUp();
    $this->seed();
  }


  public function test_can_create_book_record()
  {
    $bookData = Book::factory()->make()->toArray();
    $book = Book::create($bookData);

    $this->assertInstanceOf(Book::class, $book);
    $this->assertEquals($bookData['title'], $book->title);
    $this->assertDatabaseHas('books', $bookData);
  }

  public function test_cannot_create_book_with_missing_required_fields()
  {
    // Missing title
    $bookData = Book::factory()->make(['title' => null])->toArray();
    $this->expectException(\Illuminate\Database\QueryException::class);
    Book::create($bookData);

    // Missing author (assuming author is required)
    $bookData = Book::factory()->make(['author' => null])->toArray();
    $this->expectException(\Illuminate\Database\QueryException::class);
    Book::create($bookData);

    // Missing price 
    $bookData = Book::factory()->make(['price' => null])->toArray();
    $this->expectException(\Illuminate\Database\QueryException::class);
    Book::create($bookData);

    // Missing ISBN
    $bookData = Book::factory()->make(['isbn' => null])->toArray();
    $this->expectException(\Illuminate\Database\QueryException::class);
    Book::create($bookData);
  }

  // public function test_cannot_create_book_with_invalid_data()
  // {
  //   // Invalid ISBN (too short)
  //   $bookData = Book::factory()->make(['isbn' => '123'])->toArray();
  //   $this->expectException(\Illuminate\Database\QueryException::class);
  //   Book::create($bookData);

  //   // Negative price (if not allowed)
  //   $bookData = Book::factory()->make(['price' => -10])->toArray();
  //   $this->expectException(\Illuminate\Database\QueryException::class);
  //   Book::create($bookData);
  // }

  public function test_can_retrieve_book_by_id()
  {
    $book = Book::factory()->create();
    $retrievedBook = Book::find($book->id);

    $this->assertInstanceOf(Book::class, $retrievedBook);
    $this->assertEquals($book->id, $retrievedBook->id);
  }

  public function test_can_retrieve_all_books()
  {
    Book::factory(5)->create();
    $books = Book::all();

    $this->assertCount(5, $books);
  }

  public function test_retrieving_non_existing_book_returns_null()
  {
    $book = Book::find(9999); // Non-existing ID
    $this->assertNull($book);
  }

  public function test_can_update_book_attributes()
  {
    $book = Book::factory()->create();
    $updatedTitle = 'Updated Title';
    $book->title = $updatedTitle;
    $book->save();

    $this->assertEquals($updatedTitle, $book->fresh()->title); // Use fresh() to get the updated data
    $this->assertDatabaseHas('books', ['title' => $updatedTitle]);
  }

  public function test_cannot_update_book_with_missing_required_fields()
  {
    $book = Book::factory()->create();

    // Missing title
    $book->title = null;
    $this->expectException(\Illuminate\Database\QueryException::class);
    $book->save();
  }

  // public function test_cannot_update_book_with_invalid_data()
  // {
  //   $book = Book::factory()->create();

  //   // Invalid ISBN
  //   $book->isbn = '123';
  //   $this->expectException(\Illuminate\Database\QueryException::class);
  //   $book->save();
  // }

  public function test_can_delete_book()
  {
    $book = Book::factory()->create();
    $book->delete();

    $this->assertDatabaseMissing('books', ['id' => $book->id]);
  }

  public function test_deleting_non_existing_book_does_not_throw_exception()
  {
    Book::destroy(9999); // Non-existing ID
    $this->assertTrue(true); // Just checking that no exception is thrown
  }

  // Relationship Tests (User-Role)

  public function test_user_belongs_to_a_role()
  {
    $user = User::factory()->create();
    $this->assertInstanceOf(Role::class, $user->role);
  }

  public function test_can_retrieve_user_role()
  {
    $user = User::factory()->create();
    $roleName = $user->role->name;

    $this->assertIsString($roleName);
    // You can add further assertions based on your role data 
  }


  // Query Tests (Examples)

  public function test_can_filter_books_by_title()
  {
    Book::factory()->create(['title' => 'Laravel for Beginners']);
    Book::factory()->create(['title' => 'Advanced Laravel']);
    Book::factory()->create(['title' => 'PHP Basics']);

    $laravelBooks = Book::where('title', 'like', '%Laravel%')->get();
    $this->assertCount(2, $laravelBooks);
  }

  public function test_can_sort_books_by_title_ascending()
  {
    Book::factory()->create(['title' => 'C++ Programming']);
    Book::factory()->create(['title' => 'Java Fundamentals']);
    Book::factory()->create(['title' => 'Python Basics']);

    $sortedBooks = Book::orderBy('title', 'asc')->get();
    $this->assertEquals('C++ Programming', $sortedBooks[0]->title);
    $this->assertEquals('Java Fundamentals', $sortedBooks[1]->title);
    $this->assertEquals('Python Basics', $sortedBooks[2]->title);
  }

  // ... Add more query tests for filtering, sorting, pagination, and search

  // Data Integrity Tests

  public function test_cannot_create_book_with_duplicate_isbn()
  {
    $book1 = Book::factory()->create();

    $bookData = Book::factory()->make(['isbn' => $book1->isbn])->toArray();
    $this->expectException(\Illuminate\Database\QueryException::class);
    Book::create($bookData);
  }


  public function test_cannot_create_user_with_duplicate_email()
  {
    $user1 = User::factory()->create();
    $userData = User::factory()->make(['email' => $user1->email])->toArray();

    $this->expectException(\Illuminate\Database\QueryException::class);
    User::create($userData);
  }

  // ... Add more data integrity tests for foreign key constraints

  // Database Transactions Tests

  public function test_database_transactions_rollback_on_failure()
  {
    try {
      DB::transaction(function () {
        Book::create(['title' => 'Test Book', 'author' => 'Test Author', 'price' => 25, 'isbn' => 'invalid isbn']); // Invalid ISBN will cause an exception
      });
    } catch (\Exception $e) {
      // Exception is expected 
    }

    $this->assertDatabaseMissing('books', ['title' => 'Test Book']); // Check if the book was not created 
  }

  // public function test_database_transactions_commit_on_success()
  // {
  //   DB::transaction(function () {
  //     Book::create(['title' => 'Test Book', 'author' => 'Test Author', 'price' => 25, 'isbn' => '9781234567890']);
  //   });

  //   $this->assertDatabaseHas('books', ['title' => 'Test Book']); // Check if the book was created
  // }
}