<?php

declare(strict_types=1);

namespace App\Model\Entity;

final class SearchResult
{
    public function __construct(
        public string $title,
        public string $url,
        public string $description
    ) {
    }
}