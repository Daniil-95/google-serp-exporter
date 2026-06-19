<?php declare(strict_types=1);

namespace App\FrontModule\Presenters;

use App\Model\Entity\SearchResult;
use App\Model\Service\SearchService;
use Nette\Application\Responses\TextResponse;
use Nette\Application\UI\Form;
use Nette\Http\SessionSection;

final class HomePresenter extends BasePresenter
{
    public function __construct(
        private SearchService $searchService
    ) {
        parent::__construct();
    }

    public function renderDefault(): void
    {
        $section = $this->getSearchSection();

        $this->template->results = $section->results ?? [];
        $this->template->lastKeyword = $section->lastKeyword ?? null;
        $this->template->searchHistory = $section->history ?? [];
    }

    protected function createComponentSearchForm(): Form
    {
        $form = new Form;

        $form->addProtection();

        $form->addText('keyword', 'Klíčové slovo:')
            ->setRequired('Zadejte klíčové slovo.');

        $form->addSubmit('search', 'Vyhledat');

        $form->onSuccess[] = $this->searchFormSucceeded(...);

        return $form;
    }

    public function searchFormSucceeded(Form $form, \stdClass $values): void
    {
        try {
            $results = $this->searchService->search($values->keyword);

            $section = $this->getSearchSection();

            $section->results = $results;
            $section->lastKeyword = $values->keyword;

            $this->updateSearchHistory($values->keyword);

            $this->redirect('default');

        } catch (\RuntimeException $e) {
            $this->flashMessage($e->getMessage(), 'error');
            $this->redirect('this');
        }
    }

    public function actionNewSearch(): void
    {
        $section = $this->getSearchSection();

        unset(
            $section->results,
            $section->lastKeyword
        );

        $this->redirect('default');
    }

    public function actionExportJson(): void
    {
        $section = $this->getSearchSection();
        $results = $section->results ?? [];

        if ($results === []) {
            $this->flashMessage('Nejprve proveďte vyhledávání.', 'error');
            $this->redirect('default');
        }

        $data = array_map(
            static fn (SearchResult $result): array => [
                'title' => $result->title,
                'url' => $result->url,
                'description' => $result->description,
            ],
            $results
        );

        $json = json_encode(
            $data,
            JSON_PRETTY_PRINT
            | JSON_UNESCAPED_UNICODE
            | JSON_THROW_ON_ERROR
        );

        $this->getHttpResponse()->setContentType(
            'application/json',
            'utf-8'
        );

        $this->getHttpResponse()->setHeader(
            'Content-Disposition',
            'attachment; filename="search-results.json"'
        );

        $this->sendResponse(new TextResponse($json));
    }

    private function updateSearchHistory(string $keyword): void
    {
        $section = $this->getSearchSection();

        $history = $section->history ?? [];

        array_unshift($history, $keyword);

        $history = array_values(array_unique($history));

        $section->history = array_slice($history, 0, 5);
    }

    private function getSearchSection(): SessionSection
    {
        return $this->getSession('App\\Search');
    }
}