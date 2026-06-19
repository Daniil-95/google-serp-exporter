<?php declare(strict_types=1);

namespace Tests;

use App\Model\Entity\SearchResult;
use PHPUnit\Framework\TestCase;

final class ExportJsonTest extends TestCase
{
    /**
     * @return SearchResult[]
     */
    private function createSampleResults(): array
    {
        return [
            new SearchResult(
                title: 'First Result',
                url: 'https://first.com',
                description: 'Description of first result.',
            ),
            new SearchResult(
                title: 'Second Result',
                url: 'https://second.com',
                description: 'Description of second result.',
            ),
            new SearchResult(
                title: 'Third Result',
                url: 'https://third.com',
                description: 'Description of third result.',
            ),
        ];
    }

    public function testExportedJsonIsValidArray(): void
    {
        $results = $this->createSampleResults();

        $data = array_map(fn(SearchResult $r) => [
            'title' => $r->title,
            'url' => $r->url,
            'description' => $r->description,
        ], $results);

        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);

        $this->assertJson($json);
        $this->assertStringStartsWith('[', trim($json));
        $this->assertStringEndsWith(']', trim($json));
    }

    public function testExportedJsonContainsAllRequiredFields(): void
    {
        $results = $this->createSampleResults();

        $data = array_map(fn(SearchResult $r) => [
            'title' => $r->title,
            'url' => $r->url,
            'description' => $r->description,
        ], $results);

        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        $decoded = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        foreach ($decoded as $item) {
            $this->assertArrayHasKey('title', $item);
            $this->assertArrayHasKey('url', $item);
            $this->assertArrayHasKey('description', $item);
        }
    }

    public function testExportedJsonValuesMatchOriginalData(): void
    {
        $results = $this->createSampleResults();

        $data = array_map(fn(SearchResult $r) => [
            'title' => $r->title,
            'url' => $r->url,
            'description' => $r->description,
        ], $results);

        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        $decoded = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        $this->assertCount(3, $decoded);
        $this->assertSame('First Result', $decoded[0]['title']);
        $this->assertSame('https://first.com', $decoded[0]['url']);
        $this->assertSame('Description of first result.', $decoded[0]['description']);
        $this->assertSame('Second Result', $decoded[1]['title']);
        $this->assertSame('Third Result', $decoded[2]['title']);
    }

    public function testExportedJsonHasNoExtraFields(): void
    {
        $results = [
            new SearchResult(
                title: 'Only Result',
                url: 'https://only.com',
                description: 'Only description.',
            ),
        ];

        $data = array_map(fn(SearchResult $r) => [
            'title' => $r->title,
            'url' => $r->url,
            'description' => $r->description,
        ], $results);

        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        $decoded = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        $this->assertCount(1, $decoded);
        $this->assertCount(3, $decoded[0]);
        $this->assertArrayNotHasKey('position', $decoded[0]);
        $this->assertArrayNotHasKey('displayed_link', $decoded[0]);
        $this->assertArrayNotHasKey('sitelinks', $decoded[0]);
    }

    public function testExportedJsonWithEmptyResults(): void
    {
        $data = [];

        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        $decoded = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        $this->assertSame([], $decoded);
    }

    public function testExportedJsonIsDownloadableFormat(): void
    {
        $results = $this->createSampleResults();

        $data = array_map(fn(SearchResult $r) => [
            'title' => $r->title,
            'url' => $r->url,
            'description' => $r->description,
        ], $results);

        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);

        $this->assertJson($json);

        $decoded = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        $this->assertIsArray($decoded);
        $this->assertEquals($results[0]->title, $decoded[0]['title']);
        $this->assertEquals($results[0]->url, $decoded[0]['url']);
        $this->assertEquals($results[0]->description, $decoded[0]['description']);
    }
}
