<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Gate;
use App\Models\Book;
use App\Http\Requests\BookFormRequest;
use App\Http\Resources\BookResource;
use App\Services\OpenSearchService;
use Illuminate\Http\Request;

class BookController extends Controller
{

  public function index(Request $request)
  {
    $perPage = $request->query('per_page', 10);
    $books = Book::paginate($perPage);

    return BookResource::collection($books)
      ->additional([
        'meta' => [
          'current_page' => $books->currentPage(),
          'last_page' => $books->lastPage(),
          'per_page' => $books->perPage(),
          'total' => $books->total(),
        ],
      ]);
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    //
  }


  /**
   * Store a newly created resource in storage.
   */
  public function store(BookFormRequest $request)
  {
    $validated = $request->validated();
    $book = Book::create($validated);

    return response()->json([
      'book' => $book,
      'success' => 'Book created successfully!'
    ], 201);
  }

  /**
   * Display the specified resource.
   */
  public function show(Book $book)
  {
    return response()->json($book, 200);
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(Book $book)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(BookFormRequest $request, Book $book)
  {
    $validated = $request->validated();

    $book->update($validated);
    return response()->json(['book' => $book, 'success' => 'Book updated successfully!'], 200);
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(Book $book)
  {
    $book->delete();
    return response()->json([
      'success' => 'Book deleted successfully!'
    ], 200);
  }

  /**
   * Search for books by title, author, or ISBN.
   */

  public function search(Request $request)
  {
    $query = $request->input('q');

    if (!$query) {
      return response()->json(['message' => 'Missing search query'], 400);
    }

    $books = Book::where('title', 'LIKE', "%{$query}%")
      ->orWhere('author', 'LIKE', "%{$query}%")
      ->orWhere('isbn', 'LIKE', "%{$query}%")
      ->paginate(10);

    if ($books->isEmpty()) {
      return response()->json(['message' => 'No books found matching your search criteria'], 404);
    }

    return BookResource::collection($books);
  }
}