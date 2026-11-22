<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\App;
use App\Core\Auth;
use App\Core\Controller;
use App\Core\Paginator;
use App\Core\View;
use App\Models\Article;

final class ArticlesController extends Controller
{
    /** Список статей с поиском и пагинацией */
    public function index(): void
    {
        $q = trim((string) ($_GET['q'] ?? ''));
        $perPage = (int) App::config('per_page.articles', 4);
        $page = Paginator::currentPage();

        $result = Article::search($q, $page, $perPage);
        $pager  = new Paginator($result['total'], $page, $perPage, 'articles', ['q' => $q]);
        if ($page > $pager->pages && $result['total'] > 0) {
            $result = Article::search($q, $pager->page, $perPage);
        }

        $this->render('articles/index', [
            'title'    => 'Статьи',
            'articles' => $result['items'],
            'pager'    => $pager,
            'q'        => $q,
        ]);
    }

    /** Одна статья с ником автора */
    public function show(int $id): void
    {
        $article = Article::find($id);
        if (!$article) {
            $this->notFound('Статья не найдена');
            return;
        }

        $this->render('articles/show', [
            'title'   => $article['title'],
            'article' => $article,
            'canEdit' => Auth::id() === (int) $article['author_id'],
        ]);
    }

    public function create(): void
    {
        $this->requireLogin();
        $this->render('articles/form', [
            'title'   => 'Новая статья',
            'article' => ['title' => '', 'excerpt' => '', 'body' => ''],
            'action'  => url('articles/new'),
            'errors'  => [],
        ]);
    }

    public function store(): void
    {
        $this->requireLogin();
        $this->verifyCsrf();

        $data = $this->articleInput();
        $errors = Article::validate($data['title'], $data['excerpt'], $data['body']);

        if ($errors) {
            $this->render('articles/form', [
                'title'   => 'Новая статья',
                'article' => $data,
                'action'  => url('articles/new'),
                'errors'  => $errors,
            ], 422);
            return;
        }

        $id = Article::create(Auth::id(), $data['title'], $data['excerpt'], $data['body']);
        flash('success', 'Статья опубликована.');
        $this->redirect('articles/' . $id);
    }

    public function edit(int $id): void
    {
        $article = $this->ownArticle($id);
        if (!$article) {
            return;
        }

        $this->render('articles/form', [
            'title'   => 'Редактирование статьи',
            'article' => $article,
            'action'  => url('articles/' . $id . '/edit'),
            'errors'  => [],
        ]);
    }

    public function update(int $id): void
    {
        $article = $this->ownArticle($id);
        if (!$article) {
            return;
        }
        $this->verifyCsrf();

        $data = $this->articleInput();
        $errors = Article::validate($data['title'], $data['excerpt'], $data['body']);

        if ($errors) {
            $this->render('articles/form', [
                'title'   => 'Редактирование статьи',
                'article' => $data + $article,
                'action'  => url('articles/' . $id . '/edit'),
                'errors'  => $errors,
            ], 422);
            return;
        }

        Article::update($id, $data['title'], $data['excerpt'], $data['body']);
        flash('success', 'Изменения сохранены.');
        $this->redirect('articles/' . $id);
    }

    public function delete(int $id): void
    {
        $article = $this->ownArticle($id);
        if (!$article) {
            return;
        }
        $this->verifyCsrf();

        Article::delete($id);
        flash('success', 'Статья «' . $article['title'] . '» удалена.');
        $this->redirect('articles');
    }

    /** Статья, которую текущий пользователь имеет право менять (иначе 404/403) */
    private function ownArticle(int $id): ?array
    {
        $this->requireLogin();

        $article = Article::find($id);
        if (!$article) {
            $this->notFound('Статья не найдена');
            return null;
        }
        if ((int) $article['author_id'] !== Auth::id()) {
            View::error(403, 'Редактировать статью может только её автор');
            return null;
        }
        return $article;
    }

    private function articleInput(): array
    {
        return [
            'title'   => $this->input('title'),
            'excerpt' => $this->input('excerpt'),
            'body'    => str_replace("\r\n", "\n", $this->input('body')),
        ];
    }
}
