<?php declare(strict_types=1);

namespace App\FrontModule\Presenters;

use Nette;
use Nette\Application\Attributes\Requires;


/**
 * Zpracovává 4xx HTTP chyby.
 */
#[Requires(methods: '*', forward: true)]
final class Error4xxPresenter extends BasePresenter
{
	public function renderDefault(Nette\Application\BadRequestException $exception): void
	{
		$code = $exception->getCode();

		$templateDir = __DIR__ . '/../templates/Error/Error4xx';

		$file = is_file($file = $templateDir . "/$code.latte")
			? $file
			: $templateDir . '/4xx.latte';

		$this->template->httpCode = $code;
		$this->template->setFile($file);
	}
}
