<?php declare(strict_types=1);

namespace App\Model\Service;

use App\Model\Entity\SearchResult;

interface SearchService
{
    /**
     * @return SearchResult[]
     */
    public function search(string $keyword): array;
}