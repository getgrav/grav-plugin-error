<?php
namespace Grav\Plugin;

use Composer\Autoload\ClassLoader;
use Grav\Common\Page\Interfaces\PageInterface;
use Grav\Common\Plugin;
use Grav\Common\Page\Page;
use Grav\Common\Page\Pages;
use Grav\Common\Page\Types;
use RocketTheme\Toolbox\Event\Event;

class ErrorPlugin extends Plugin
{
    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'onCliInitialize' => [
                ['autoload', 100000],
            ],
            'onPageNotFound' => [
                ['onPageNotFound', 0]
            ],
            'onGetPageTemplates' => [
                ['onGetPageTemplates', 0]
            ],
            'onTwigTemplatePaths' => [
                ['onTwigTemplatePaths', -10]
            ],
            'onDisplayErrorPage.404'=> [
                ['onDisplayErrorPage404', -1]
            ]
        ];
    }

    /**
     * [onPluginsInitialized:100000] Composer autoload.
     *
     * @return ClassLoader
     */
    public function autoload(): ClassLoader
    {
        return require __DIR__ . '/vendor/autoload.php';
    }

    /**
     * @param Event $event
     */
    public function onDisplayErrorPage404(Event $event): void
    {
        if ($this->isAdmin()) {
            return;
        }

        $event['page'] = $this->getErrorPage();
        $event->stopPropagation();
    }

    /**
     * Display error page if no page was found for the current route.
     *
     * @param Event $event
     */
    public function onPageNotFound(Event $event): void
    {
        $event->page = $this->getErrorPage();
        $event->stopPropagation();
    }

    /**
     * @return PageInterface
     * @throws \Exception
     */
    public function getErrorPage(): PageInterface
    {
        /** @var Pages $pages */
        $pages = $this->grav['pages'];

        // Try to load user error page.
        $page = $pages->dispatch($this->config->get('plugins.error.routes.404', '/error'), true);
        if (!$page) {
            // If none provided use built in error page.
            $language = $this->grav['language'];
            $page = new Page;
            $page->init(new \SplFileInfo(__DIR__ . '/pages/error.md'));
            $page->title($language->translate('PLUGIN_ERROR.ERROR') . ' ' . $page->header()->http_response_code);

            // Supply the (translated) message from PHP rather than from page
            // content. Grav 2 disables Twig-in-content by default
            // (security.twig_content.process_enabled), so a `{{ ...|t }}` in the
            // body would render literally on the 404 page (#47). Only fill it in
            // when the body is empty, so a custom message still takes precedence.
            // Check the raw markdown, not content(): calling the content getter
            // here would process and cache the empty body, and that cached empty
            // string would then be served instead of the message we set below.
            if (trim((string) $page->rawMarkdown()) === '') {
                $page->content($language->translate('PLUGIN_ERROR.ERROR_MESSAGE'));
            }
        }

        // Login page may not have the correct Cache-Control header set, force no-store for the proxies.
        $cacheControl = $page->cacheControl();
        if (!$cacheControl) {
            $page->cacheControl('private, no-cache, must-revalidate');
        }

        return $page;
    }

    /**
     * Add page template types.
     */
    public function onGetPageTemplates(Event $event): void
    {
        /** @var Types $types */
        $types = $event->types;
        $types->register('error');
    }

    /**
     * Add current directory to twig lookup paths.
     */
    public function onTwigTemplatePaths(): void
    {
        $this->grav['twig']->twig_paths[] = __DIR__ . '/templates';
    }
}
