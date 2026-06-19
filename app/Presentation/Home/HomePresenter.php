<?php declare(strict_types=1);

namespace App\Presentation\Home;

use App\Model\Service\SearchService;
use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;

final class HomePresenter extends Presenter
{
    private array $results = [];

    public function __construct(
        private SearchService $searchService
    ) {
        parent::__construct();
    }

    public function renderDefault(): void
    {
        $this->template->results = $this->results;
    }

    protected function createComponentSearchForm(): Form
    {
        $form = new Form;

        $form->addText('keyword', 'Klíčové slovo:')
            ->setRequired('Zadejte klíčové slovo.');

        $form->addSubmit('search', 'Vyhledat');

        $form->onSuccess[] = $this->searchFormSucceeded(...);

        return $form;
    }

    public function searchFormSucceeded(Form $form, \stdClass $values): void
    {
        $this->results = $this->searchService->search(
            $values->keyword
        );

        $this->template->results = $this->results;
    }
}