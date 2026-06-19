<?php declare(strict_types=1);

namespace App;

use Nette;
use Nette\Bootstrap\Configurator;


class Bootstrap
{
	private readonly Configurator $configurator;
	private readonly string $rootDir;


	public function __construct()
	{
		$this->rootDir = dirname(__DIR__);
		$this->configurator = new Configurator;
		$this->configurator->setTempDirectory($this->rootDir . '/temp');
	}


	public function bootWebApplication(): Nette\DI\Container
	{
		$this->initializeEnvironment();
		$this->setupContainer();
		return $this->configurator->createContainer();
	}


	public function initializeEnvironment(): void
	{
		//$this->configurator->setDebugMode('secret@23.75.345.200'); // enable for your remote IP
		$this->configurator->enableTracy($this->rootDir . '/log');

		$this->loadEnvironmentFile();

        $this->configurator->addDynamicParameters([
            'serpApiKey' => getenv('SERPAPI_API_KEY') ?: '',
        ]);

		$this->configurator->createRobotLoader()
			->addDirectory(__DIR__)
			->register();
	}


	private function loadEnvironmentFile(): void
	{
		$envFile = $this->rootDir . '/.env';

		if (!is_file($envFile)) {
			return;
		}

		$lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

		foreach ($lines as $line) {
			$line = trim($line);

			if ($line === '' || str_starts_with($line, '#')) {
				continue;
			}

			if (!str_contains($line, '=')) {
				continue;
			}

			[$key, $value] = explode('=', $line, 2);
			$key = trim($key);
			$value = trim($value);

			if ($value === '') {
				continue;
			}

			// Strip surrounding quotes if present
			if (strlen($value) >= 2) {
				$first = $value[0];
				$last = $value[strlen($value) - 1];
				if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
					$value = substr($value, 1, -1);
				}
			}

			putenv("$key=$value");
			$_ENV[$key] = $value;
		}
	}


	private function setupContainer(): void
	{
		$configDir = $this->rootDir . '/config';
		$this->configurator->addConfig($configDir . '/common.neon');
		$this->configurator->addConfig($configDir . '/services.neon');
	}
}
