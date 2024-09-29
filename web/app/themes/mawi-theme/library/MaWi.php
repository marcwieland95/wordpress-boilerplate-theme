<?php

declare(strict_types=1);

namespace MaWi;

use MaWi\PostType\AbstractPostType;
use MaWi\PostType\CustomPostTypeInterface;
use Timber\Timber;

class MaWi
{
    /**
     * @var self|null Klasseninstanz
     */
    protected static $instance = null;

    public function __construct()
    {
        // Initialize Timber.
        Timber::init();

        new Hook\Cleanup();
        new Hook\Gutenberg();
        new Hook\Query();
        new Hook\RestApi();
        new Hook\Script();
        new Hook\Admin();
        new Hook\Setup();

        new Hook\Plugin\Plugin();

        $this->initializePosts();
    }

    public static function getDeploymentEnvironment()
    {
        if (in_array(getenv('DEPLOYMENT_ENV'), ['PRODUCTION', 'STAGING'], true)) {
            return getenv('DEPLOYMENT_ENV');
        }

        return 'DEVELOPMENT';
    }

    /**
     * Singleton Init class
     * @return self
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    protected function initializePosts()
    {
        $postFiles = glob(__DIR__ . DIRECTORY_SEPARATOR . 'PostType/*.php');

        usort($postFiles, function ($a, $b) {
            if (strpos($a, 'PostType/Post.php') !== false) {
                return 1;
            }
            if (strpos($b, 'PostType/Post.php') !== false) {
                return -1;
            }

            return 0;
        });

        foreach ($postFiles as $postFile) {
            $exclusions = [
                AbstractPostType::class,
                CustomPostTypeInterface::class,
                /*
                CustomRewriteRuleInterface::class,
                BlockContentsTrait::class,
                FsTagsInterface::class,
                CacheableInterface::class,
                SimpleCacheablePostTrait::class,
                */
            ];
            $class = 'MaWi\\PostType\\' . basename($postFile, '.php');
            if (in_array($class, $exclusions, true)) {
                continue;
            }

            /** @var AbstractPostType $postClass */
            $postClass = new $class();

            if ($postClass instanceof CustomPostTypeInterface) {
                add_action('init', [$postClass, 'registerPostType']);
            }

            /*
            if ($postClass instanceof CustomRewriteRuleInterface) {
                $postClass->registerRewriteRule();
                add_filter('query_vars', [$postClass, 'addQueryVars']);
            }

            if ($postClass instanceof CacheableInterface) {
                $this->cachedObjects[] = $class;
            }
            */
        }
    }
}
