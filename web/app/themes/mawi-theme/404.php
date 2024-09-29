<?php

declare(strict_types=1);

use MaWi\Helper\QueryHelper;
use Timber\PostQuery;
use Timber\Timber;

$context = Timber::context();
$templates = ['index.html.twig'];

$query = QueryHelper::get404Page();
$context['posts'] = new PostQuery($query);
$context['page'] = $query;

Timber::render($templates, $context);
