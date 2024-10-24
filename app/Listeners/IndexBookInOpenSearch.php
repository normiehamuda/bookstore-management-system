<?php

namespace App\Listeners;

use App\Events\BookCreated;
use App\Events\BookCreatedEvent;
use App\Services\OpenSearchService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class IndexBookInOpenSearch
{

  private $opensearchService;

  /**
   * Create the event listener.
   */
  public function __construct(OpenSearchService $opensearchService)
  {
    $this->opensearchService = $opensearchService;
  }

  /**
   * Handle the event.
   */
  public function handle(BookCreatedEvent $event): void
  {
    $book = $event->book;

    // Index the book data in OpenSearch
    $this->opensearchService->indexBook($book);
  }
}