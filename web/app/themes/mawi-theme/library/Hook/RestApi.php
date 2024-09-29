<?php

declare(strict_types=1);

namespace MaWi\Hook;

use MaWi\Helper\QueryHelper;
use MaWi\Helper\TemplateHelper;
use Timber\Timber;

class RestApi
{
    public function __construct()
    {
        //add_action('rest_api_init', [$this, 'teaserEndpoint']);
    }

    /*
    public function teaserEndpoint()
    {
        register_rest_route('mawi/v1', '/moreTeaser/(?P<id>\d+)', [
            'methods' => \WP_REST_SERVER::READABLE,
            'callback' => [$this, 'getMoreTeaser'],
            'permission_callback' => '__return_true',
        ]);
    }

    public function getMoreTeaser(\WP_REST_Request $request)
    {
        $paged = $request->get_param('id');
        $exclude = json_decode($request->get_param('exclude'));

        if (empty($cases)) {
            return new \WP_REST_Response([], 404);
        }

        $view = Timber::compile('modules/circlelist.html.twig', [
            'items' => 'xyz',
        ]);

        return [
            'paged' => 'xyz',
            'view' => $view,
        ];
    }
    */
}
