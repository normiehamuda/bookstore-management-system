<?php

namespace App\Services;

use App\Models\Book;
use OpenSearch\ClientBuilder;


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
          'from' => ($page - 1) * $perPage,
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


      $formattedResults = $this->formatResults($results);

      return $formattedResults;
    } catch (\Exception $e) {
      return [
        'error' => 'An error occurred while searching.',
        'message' => $e->getMessage(),
      ];
    }
  }

  public function indexBook(Book $book)
  {
    $params = [
      'index' => 'books',
      'id'    => $book->id,
      'body'  => $book->toArray(),
    ];
    $this->client->index($params);
  }

  private function formatResults(array $results): array
  {

    $formattedResults = [
      'data' => [],
      'meta' => [
        'current_page' => 1,
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
      print('OpenSearch Error: ' . $e->getMessage());
    }

    return $formattedResults;
  }
}