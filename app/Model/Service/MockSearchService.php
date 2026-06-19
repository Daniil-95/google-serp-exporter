<?php declare(strict_types=1);

namespace App\Model\Service;

use App\Model\Entity\SearchResult;

final class MockSearchService implements SearchService
{
    /**
     * @return SearchResult[]
     */
    public function search(string $keyword): array
    {
        return [
            new SearchResult(
                title: 'Apple',
                url: 'https://apple.com',
                description: 'Official Apple website'
            ),

            new SearchResult(
                title: 'Alza',
                url: 'https://alza.cz',
                description: 'Largest Czech e-shop'
            ),
        ];
    }
}