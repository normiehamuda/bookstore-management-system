<?php

namespace App\Http\Controllers;

use App\Events\BookCreatedEvent;
use Illuminate\Support\Facades\Gate;
use App\Models\Book;
use App\Http\Requests\BookFormRequest;
use App\Http\Resources\BookResource;
use App\Services\OpenSearchService;
use Illuminate\Http\Request;

/**
 * @OAS\SecurityScheme(
 *      securityScheme="bearer_token",
 *      type="http",
 *      scheme="bearer"
 * )
 */
class BookController extends Controller
{

  private $opensearchService;

  public function __construct(OpenSearchService $opensearchService)
  {
    $this->opensearchService = $opensearchService;
  }
  /**
   * @OA\Get(
   *     path="/api/books",
   *     summary="Get a list of all books",
   *     tags={"Books"},
   *     @OA\Response(
   *         response=200,
   *         description="Successful operation",
   *         @OA\JsonContent(
   *             type="array",
   *             @OA\Items(ref="#/components/schemas/Book") 
   *         )
   *     )
   * )
   */
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
   * @OA\Post(
   *     path="/api/books",
   *     summary="Create a new book",
   *     tags={"Books"},
   *     security={{"bearer_token":{}}},
   *     @OA\RequestBody(
   *         required=true,
   *         @OA\JsonContent(ref="#/components/schemas/BookRequest") 
   *     ),
   *     @OA\Response(
   *         response=201,
   *         description="Book created successfully"
   *     ),
   *     @OA\Response(
   *         response=422,
   *         description="Validation error"
   *     )
   * )
   */

  public function store(BookFormRequest $request)
  {
    $validated = $request->validated();
    $book = Book::create($validated);

    event(new BookCreatedEvent($book));

    return response()->json([
      'book' => $book,
      'success' => 'Book created successfully!'
    ], 201);
  }

  /**
   * @OA\Get(
   *     path="/api/books/{book}",
   *     summary="Get book details",
   *     tags={"Books"},
   *     @OA\Parameter(
   *         name="book",
   *         in="path",
   *         description="Book ID",
   *         required=true,
   *         @OA\Schema(type="integer")
   *     ),
   *     @OA\Response(
   *         response=200,
   *         description="Successful operation",
   *         @OA\JsonContent(ref="#/components/schemas/Book")
   *     ),
   *     @OA\Response(
   *         response=404,
   *         description="Book not found"
   *     )
   * )
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
   * @OA\Put(
   *     path="/api/books/{book}",
   *     summary="Update a book",
   *     tags={"Books"},
   *     @OA\Parameter(
   *         name="book",
   *         in="path",
   *         description="Book ID",
   *         required=true,
   *         @OA\Schema(type="integer")
   *     ),
   *     security={{"bearer_token":{}}},
   *     @OA\RequestBody(
   *         required=true,
   *         @OA\JsonContent(ref="#/components/schemas/BookRequest")
   *     ),
   *     @OA\Response(
   *         response=200,
   *         description="Book updated successfully"
   *     ),
   *     @OA\Response(
   *         response=404,
   *         description="Book not found"
   *     ),
   *     @OA\Response(
   *         response=422,
   *         description="Validation error"
   *     )
   * )
   */
  public function update(BookFormRequest $request, Book $book)
  {
    $validated = $request->validated();

    $book->update($validated);
    return response()->json(['book' => $book, 'success' => 'Book updated successfully!'], 200);
  }

  /**
   * @OA\Delete(
   *     path="/api/books/{book}",
   *     summary="Delete a book",
   *     tags={"Books"},
   *     security={{"bearer_token":{}}},
   *     @OA\Parameter(
   *         name="book",
   *         in="path",
   *         description="Book ID",
   *         required=true,
   *         @OA\Schema(type="integer")
   *     ),
   *     @OA\Response(
   *         response=200,
   *         description="Book deleted successfully"
   *     ),
   *     @OA\Response(
   *         response=404,
   *         description="Book not found"
   *     )
   * )
   */
  public function destroy(Book $book)
  {
    $book->delete();
    return response()->json([
      'success' => 'Book deleted successfully!'
    ], 200);
  }

  /**
   * @OA\Get(
   *     path="/api/books/search",
   *     summary="Search books",
   *     tags={"Books"},
   *     @OA\Parameter(
   *         name="q",
   *         in="query",
   *         description="Search query (title, author, or ISBN)",
   *         required=true,
   *         @OA\Schema(type="string")
   *     ),
   *     @OA\Response(
   *         response=200,
   *         description="Successful operation",
   *         @OA\JsonContent(
   *             type="array",
   *             @OA\Items(ref="#/components/schemas/Book")
   *         )
   *     ),
   *     @OA\Response(
   *         response=404,
   *         description="No books found" 
   *     )
   * )
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

  /**
   * @OA\Get(
   *     path="/api/books/elastic-search",
   *     summary="Search books (OpenSearch)",
   *     tags={"Books"},
   *     @OA\Parameter(
   *         name="q",
   *         in="query",
   *         description="Search query",
   *         required=true,
   *         @OA\Schema(type="string")
   *     ),
   *     @OA\Parameter(
   *         name="page",
   *         in="query",
   *         description="Page number (for pagination)",
   *         @OA\Schema(type="integer") 
   *     ),
   *     @OA\Parameter(
   *         name="per_page",
   *         in="query",
   *         description="Number of results per page",
   *         @OA\Schema(type="integer")
   *     ),
   *     @OA\Response(
   *         response=200,
   *         description="Successful operation",
   *         @OA\JsonContent(
   *             type="object", 
   *             @OA\Property(
   *                 property="data",
   *                 type="array",
   *                 @OA\Items(ref="#/components/schemas/Book")
   *             ),
   *         )
   *     ),
   *     @OA\Response(
   *         response=400,
   *         description="Missing search query" 
   *     ),
   *     @OA\Response(
   *         response=500,
   *         description="OpenSearch error"
   *     ) 
   * )
   */
  public function elasticSearch(Request $request)
  {
    $query = $request->input('q');
    $page = $request->input('page', 1);
    $perPage = $request->input('per_page', 10);

    if (!$query) {
      return response()->json(['message' => 'Missing search query'], 400);
    }

    $results = $this->opensearchService->searchBooks($query, $page, $perPage);

    if (isset($results['error'])) {
      return response()->json($results, 500);
    }

    return response()->json($results);
  }
}