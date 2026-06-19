<?php declare(strict_types=1);

namespace App\FrontModule\Presenters;

use App\Model\Entity\SearchResult;
use App\Model\Service\SearchService;
use Nette\Application\Responses\TextResponse;
use Nette\Application\UI\Form;

final class HomePresenter extends BasePresenter
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

        $form->addProtection();

        $form->addText('keyword', 'Klíčové slovo:')
            ->setRequired('Zadejte klíčové slovo.');

        $form->addSubmit('search', 'Vyhledat');

        $form->onSuccess[] = $this->searchFormSucceeded(...);

        return $form;
    }

    public function searchFormSucceeded(Form $form, object $values): void
    {
        try {
            $this->results = $this->searchService->search(
                $values->keyword
            );

            $this->template->results = $this->results;

            $section = $this->getSession('App\Search');
            $section->results = $this->results;

        } catch (\RuntimeException $e) {
            $this->flashMessage($e->getMessage(), 'error');
            $this->redirect('this');
        }
    }


    public function actionExportJson(): void
    {
        $section = $this->getSession('App\Search');
        $results = $section->results ?? [];

        if ($results === []) {
            $this->flashMessage('Nejprve proveďte vyhledávání.', 'error');
            $this->redirect('default');
        }

        $data = array_map(function (SearchResult $result): array {
            return [
                'title' => $result->title,
                'url' => $result->url,
                'description' => $result->description,
            ];
        }, $results);

        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);

        $this->getHttpResponse()->setContentType('application/json', 'utf-8');
        $this->getHttpResponse()->setHeader('Content-Disposition', 'attachment; filename="search-results.json"');

        $this->sendResponse(new TextResponse($json));
    }
}
