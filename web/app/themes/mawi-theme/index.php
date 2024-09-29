<?php

declare(strict_types=1);

use MaWi\Helper\QueryHelper;
use Timber\PostQuery;
use Timber\Timber;

$context = Timber::context();
$templates = ['index.html.twig'];

$post = $context['post'];

/*
$query = QueryHelper::getPost();
$context['posts'] = new PostQuery($query->getPost());
$context['page'] = $query;
*/

Timber::render($templates, $context);
