<?php

namespace Postgen\Api\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Schema;
use Postgen\Common\Logic\Articles;
use Postgen\Moderator\Models\Article;

class ArticleApiController extends Controller
{
    public function store(int $id = null, string $type)
    {
        $content = request('content');

        $article = Article::findOrFail($id);
        if (auth()->id() !== 1 && $article->user_id !== auth()->id()) abort(403);
        if (!Schema::hasColumn($article->getTable(), $type)) abort(422);

        $success = Articles::updateSocial($article, $type, $content);

        return response(['success' => $success]);
    }
}
