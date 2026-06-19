<?php declare(strict_types=1);

namespace App\Model\Service;

use App\Model\Entity\SearchResult;

final class GoogleSearchService implements SearchService
{
	private const API_BASE_URL = 'https://serpapi.com/search';

	public function __construct(
		private readonly string $apiKey,
	) {
		if ($apiKey === '') {
			throw new \InvalidArgumentException('SerpApi API key cannot be empty.');
		}
	}

	/**
	 * @return SearchResult[]
	 */
	public function search(string $keyword): array
	{
		$url = $this->buildUrl($keyword);
		$response = $this->fetch($url);
		$data = $this->decodeResponse($response);
		$this->validateResponse($data);

		return $this->parseResults($data);
	}

	private function buildUrl(string $keyword): string
	{
		$params = [
			'q' => $keyword,
			'api_key' => $this->apiKey,
			'engine' => 'google',
			'google_domain' => 'google.com',
			'hl' => 'cs',
			'gl' => 'cz',
		];

		return self::API_BASE_URL . '?' . http_build_query($params);
	}

	private function fetch(string $url): string
	{
		$ch = curl_init();
		curl_setopt_array($ch, [
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_USERAGENT => 'Google-SERP-Exporter/1.0',
		]);

		$response = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$curlError = curl_error($ch);
		curl_close($ch);

		if ($response === false || $response === '') {
			throw new \RuntimeException(
				'Failed to connect to SerpApi'
				. ($curlError !== '' ? ': ' . $curlError : '')
			);
		}

		if ($httpCode !== 200) {
			$data = json_decode($response, true);
			$errorMsg = $data['error'] ?? '';
			throw new \RuntimeException(
				'SerpApi returned HTTP ' . $httpCode
				. ($errorMsg !== '' ? ': ' . $errorMsg : '')
			);
		}

		return $response;
	}

	/**
	 * @return array<string, mixed>
	 */
	private function decodeResponse(string $response): array
	{
		$data = json_decode($response, true);

		if (json_last_error() !== JSON_ERROR_NONE) {
			throw new \RuntimeException(
				'Failed to parse SerpApi response: ' . json_last_error_msg()
			);
		}

		return $data;
	}

	/**
	 * @param array<string, mixed> $data
	 */
	private function validateResponse(array $data): void
	{
		if (isset($data['error'])) {
			throw new \RuntimeException('SerpApi error: ' . $data['error']);
		}
	}

	/**
	 * @param array<string, mixed> $data
	 * @return SearchResult[]
	 */
	public function parseResults(array $data): array
	{
		if (!isset($data['organic_results']) || !is_array($data['organic_results'])) {
			return [];
		}

		$results = [];
		foreach ($data['organic_results'] as $item) {
			$results[] = new SearchResult(
				title: $item['title'] ?? '',
				url: $item['link'] ?? '',
				description: $item['snippet'] ?? '',
			);
		}

		return $results;
	}
}
