<?php declare(strict_types=1);

namespace Tests;

use App\Model\Entity\SearchResult;
use PHPUnit\Framework\TestCase;

final class SearchResultTest extends TestCase
{
    public function testCreateWithAllProperties(): void
    {
        $result = new SearchResult(
            title: 'Test Title',
            url: 'https://example.com',
            description: 'Test Description',
        );

        $this->assertSame('Test Title', $result->title);
        $this->assertSame('https://example.com', $result->url);
        $this->assertSame('Test Description', $result->description);
    }

    public function testDefaultEmptyStrings(): void
    {
        $result = new SearchResult(title: '', url: '', description: '');

        $this->assertSame('', $result->title);
        $this->assertSame('', $result->url);
        $this->assertSame('', $result->description);
    }

    public function testAllPropertiesAreStrings(): void
    {
        $result = new SearchResult(title: 'a', url: 'b', description: 'c');

        $this->assertIsString($result->title);
        $this->assertIsString($result->url);
        $this->assertIsString($result->description);
    }

    public function testImplementsInterface(): void
    {
        $result = new SearchResult(title: 'x', url: 'y', description: 'z');

        $this->assertInstanceOf(SearchResult::class, $result);
    }
}
