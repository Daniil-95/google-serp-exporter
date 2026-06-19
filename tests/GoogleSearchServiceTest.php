<?php declare(strict_types=1);

namespace Tests;

use App\Model\Entity\SearchResult;
use App\Model\Service\GoogleSearchService;
use App\Model\Service\SearchService;
use PHPUnit\Framework\TestCase;

final class GoogleSearchServiceTest extends TestCase
{
    private GoogleSearchService $service;

    protected function setUp(): void
    {
        $this->service = new GoogleSearchService('test-api-key');
    }

    public function testImplementsSearchService(): void
    {
        $this->assertInstanceOf(SearchService::class, $this->service);
    }

    public function testParseResultsReturnsEmptyArrayWhenNoOrganicResults(): void
    {
        $results = $this->service->parseResults([]);

        $this->assertSame([], $results);
    }

    public function testParseResultsReturnsEmptyArrayWhenOrganicResultsNotSet(): void
    {
        $data = [
            'search_metadata' => ['id' => '123'],
            'search_parameters' => ['q' => 'test'],
        ];

        $results = $this->service->parseResults($data);

        $this->assertSame([], $results);
    }

    public function testParseResultsMapsAllFieldsCorrectly(): void
    {
        $data = [
            'organic_results' => [
                [
                    'title' => 'Example Title',
                    'link' => 'https://example.com',
                    'snippet' => 'Example description text.',
                ],
                [
                    'title' => 'Second Result',
                    'link' => 'https://second.com',
                    'snippet' => 'Second description text.',
                ],
            ],
        ];

        $results = $this->service->parseResults($data);

        $this->assertCount(2, $results);
        $this->assertInstanceOf(SearchResult::class, $results[0]);

        $this->assertSame('Example Title', $results[0]->title);
        $this->assertSame('https://example.com', $results[0]->url);
        $this->assertSame('Example description text.', $results[0]->description);

        $this->assertSame('Second Result', $results[1]->title);
        $this->assertSame('https://second.com', $results[1]->url);
        $this->assertSame('Second description text.', $results[1]->description);
    }

    public function testParseResultsHandlesMissingFields(): void
    {
        $data = [
            'organic_results' => [
                [
                    'title' => 'Has Title',
                    'link' => 'https://example.com',
                ],
                [
                    'link' => 'https://missing-title.com',
                    'snippet' => 'Missing title',
                ],
                [
                    'title' => 'Minimal',
                ],
            ],
        ];

        $results = $this->service->parseResults($data);

        $this->assertCount(3, $results);

        $this->assertSame('Has Title', $results[0]->title);
        $this->assertSame('https://example.com', $results[0]->url);
        $this->assertSame('', $results[0]->description);

        $this->assertSame('', $results[1]->title);
        $this->assertSame('https://missing-title.com', $results[1]->url);
        $this->assertSame('Missing title', $results[1]->description);

        $this->assertSame('Minimal', $results[2]->title);
        $this->assertSame('', $results[2]->url);
        $this->assertSame('', $results[2]->description);
    }

    public function testParseResultsSkipsNonOrganicItems(): void
    {
        $data = [
            'organic_results' => [
                [
                    'title' => 'Organic One',
                    'link' => 'https://organic.com',
                    'snippet' => 'Organic snippet.',
                ],
            ],
            'local_results' => [
                [
                    'title' => 'Local Result',
                    'link' => 'https://local.com',
                ],
            ],
            'ads' => [
                [
                    'title' => 'Ad Result',
                    'link' => 'https://ad.com',
                ],
            ],
        ];

        $results = $this->service->parseResults($data);

        $this->assertCount(1, $results);
        $this->assertSame('Organic One', $results[0]->title);
    }

    public function testParseResultsWithRealisticSerpApiResponse(): void
    {
        $data = [
            'search_metadata' => [
                'id' => 'test-id',
                'status' => 'Success',
            ],
            'search_parameters' => [
                'q' => 'test query',
                'engine' => 'google',
            ],
            'organic_results' => [
                [
                    'position' => 1,
                    'title' => 'Test Result One',
                    'link' => 'https://test-one.com/page',
                    'displayed_link' => 'https://test-one.com',
                    'snippet' => 'This is the first test result description.',
                    'sitelinks' => [],
                ],
                [
                    'position' => 2,
                    'title' => 'Test Result Two',
                    'link' => 'https://test-two.com/article',
                    'displayed_link' => 'https://test-two.com',
                    'snippet' => 'This is the second test result description.',
                ],
            ],
            'pagination' => [
                'current' => 1,
                'next' => 'https://serpapi.com/search?q=test&start=10',
            ],
        ];

        $results = $this->service->parseResults($data);

        $this->assertCount(2, $results);

        $this->assertSame('Test Result One', $results[0]->title);
        $this->assertSame('https://test-one.com/page', $results[0]->url);
        $this->assertSame('This is the first test result description.', $results[0]->description);

        $this->assertSame('Test Result Two', $results[1]->title);
        $this->assertSame('https://test-two.com/article', $results[1]->url);
        $this->assertSame('This is the second test result description.', $results[1]->description);
    }
}
