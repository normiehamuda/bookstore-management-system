<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\OpenSearchService;

class IndexBooks extends Command
{
  protected $signature = 'index:books';
  protected $description = 'Index all books in OpenSearch';

  public function handle()
  {
    $opensearchService = new OpenSearchService();
    $response = $opensearchService->addBulkBookData();

    $this->info('Books indexed successfully.');
  }
}