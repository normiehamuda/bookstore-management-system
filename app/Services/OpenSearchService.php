<?php

namespace App\Services;

use App\Models\Book;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use OpenSearch\ClientBuilder;

use function Illuminate\Log\log;

class OpenSearchService
{
  private $client;

  public function __construct()
  {
    $hosts = [
      [
        'host' => env('OPENSEARCH_HOST'),
        'port' => env('OPENSEARCH_PORT'),
        'scheme' => env('OPENSEARCH_SCHEME'),
      ],
    ];

    $this->client = ClientBuilder::create()
      ->setHosts($hosts)
      ->setBasicAuthentication(env('OPENSEARCH_USERNAME'), env('OPENSEARCH_PASSWORD'))
      ->build();
  }

  public function searchBooks(string $query, int $page = 1, int $perPage = 10): array
  {
    try {
      $params = [
        'index' => 'books',
        'body' => [
          'from' => ($page - 1) * $perPage, // Calculate offset for pagination
          'size' => $perPage,
          'query' => [
            'multi_match' => [
              'query' => $query,
              'fields' => ['title', 'author', 'description', 'isbn'],
            ],
          ],
        ],
      ];

      $results = $this->client->search($params);

      // Format the results (see next section)
      $formattedResults = $this->formatResults($results);

      return $formattedResults;
    } catch (\Exception $e) {
      // Log the exception for debugging
      // \Log::error('OpenSearch Error: ' . $e->getMessage());

      // Return an appropriate error response
      return [
        'error' => 'An error occurred while searching.',
        'message' => $e->getMessage(), // You might want to hide this in production
      ];
    }
  }

  public function indexBook(Book $book)
  {
    $params = [
      'index' => 'books', // Your OpenSearch index name
      'id'    => $book->id, // Use the book's ID as the document ID
      'body'  => $book->toArray(), // Index all the book attributes
    ];

    $this->client->index($params);
  }

  private function formatResults(array $results): array
  {

    $formattedResults = [
      'data' => [],
      'meta' => [
        'current_page' => 1, // You'll need to calculate these based on pagination
        'last_page' => 1,
        'per_page' => 10,
        'total' => $results['hits']['total']['value'],
      ],
    ];

    try {
      foreach ($results['hits']['hits'] as $hit) {
        $formattedResults['data'][] = [
          'id' => $hit['_id'],
          'title' => $hit['_source']['title'],
          'author' => $hit['_source']['author'],
          'description' => $hit['_source']['description'],
          'price' => $hit['_source']['price'],
          'isbn' => $hit['_source']['isbn']
        ];
      }
    } catch (\Exception $e) {
      // Log the exception for debugging
      print('OpenSearch Error: ' . $e->getMessage());
    }

    return $formattedResults;
  }

  // // Correctly format the results to include the _id as 'id'
  // $formattedResults = collect($results['hits']['hits'])->map(function ($hit) {
  //   return array_merge(['id' => $hit['_id']], $hit['_source']);
  // })->toArray(); // Convert the collection to an array

  // return $formattedResults;
}