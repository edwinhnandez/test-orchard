<?php

namespace Drupal\menu_banner\EventSubscriber;

use Drupal;
use Drupal\Core\Menu\MenuTreeParameters;
use Drupal\Core\Path\PathAliasManagerInterface;
use Drupal\Core\Path\PathMatcher;
use Drupal\Core\Menu\MenuLinkTreeInterface;
use Drupal\Core\Path\CurrentPathStack;
use Drupal\path_alias\AliasManagerInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Render\RendererInterface;
use Exception;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\ViewEvent;

class BannerImageSubscriber implements EventSubscriberInterface {

  /**
   * The path matcher.
   *
   * @var PathMatcher
   */
  protected $pathMatcher;

  /**
   * The menu link tree service.
   *
   * @var MenuLinkTreeInterface
   */
  protected $menuLinkTree;

  /**
   * The current path.
   *
   * @var CurrentPathStack
   */
  protected $currentPath;

  /**
   * @var AliasManagerInterface $aliasManager
   */
  protected $aliasManager;

  /**
   * The current route match.
   *
   * @var RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * The renderer service.
   *
   * @var RendererInterface
   */
  protected $renderer;

  /**
   * Constructs a new BannerImageSubscriber object.
   */
  public function __construct(
    PathMatcher $path_matcher,
    MenuLinkTreeInterface $menu_link_tree,
    CurrentPathStack $current_path,
    AliasManagerInterface $alias_manager,
    RouteMatchInterface $route_match,
    RendererInterface $renderer
  ) {
    $this->pathMatcher = $path_matcher;
    $this->menuLinkTree = $menu_link_tree;
    $this->currentPath = $current_path;
    $this->aliasManager = $alias_manager;
    $this->routeMatch = $route_match;
    $this->renderer = $renderer;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      KernelEvents::VIEW => ['onView', 100],
    ];
  }

  /**
   * Handles the kernel view event.
   *
   * @param ViewEvent $event
   *   The event to process.
   */
  public function onView(ViewEvent $event) {
    try {
      // Get current path first
      $current_path = $this->currentPath->getPath();
      $alias = $this->aliasManager->getAliasByPath($current_path);

      // Load menu tree
      $parameters = new MenuTreeParameters();
      $menu_tree = $this->menuLinkTree->load('main', $parameters);
      $tree = $this->menuLinkTree->build($menu_tree);

      // Set default image
      $image = '/modules/custom/menu_banner/assets/images/default.png';

      // Check path conditions
      if (str_starts_with($alias, '/root-a')) {
        $image = '/modules/custom/menu_banner/assets/images/banner-a.png';
      }
      elseif (str_starts_with($alias, '/root-b')) {
        $image = '/modules/custom/menu_banner/assets/images/banner-b.png';
      }

      // Get the controller result and add cache dependency
      $result = $event->getControllerResult();
      if ($result) {
        $this->renderer->addCacheableDependency($result, $image);
      }

      // Set the banner image URL as a request attribute
      $event->getRequest()->attributes->set('banner_image_url', $image);
    }
    catch (Exception $e) {
      Drupal::logger('menu_banner')->error('Error in banner image subscriber: @message', [
        '@message' => $e->getMessage()
      ]);
    }
  }
}
