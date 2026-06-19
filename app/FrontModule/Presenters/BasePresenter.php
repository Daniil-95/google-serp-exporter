<?php declare(strict_types=1);

namespace App\FrontModule\Presenters;

use Nette\Application\UI\Presenter;

abstract class BasePresenter extends Presenter
{
	public function formatTemplateFiles(): array
	{
		$dir = dirname(__DIR__) . '/templates';

		return [
			"$dir/{$this->getName()}/{$this->view}.latte",
		];
	}


	public function formatLayoutTemplateFiles(): array
	{
		$dir = dirname(__DIR__) . '/templates';

		return [
			"$dir/@layout.latte",
		];
	}
}
