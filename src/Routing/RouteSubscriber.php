<?php

namespace Drupal\greencko_user\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Listens to the dynamic route events.
 */
class RouteSubscriber extends RouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection) {
    $config = \Drupal::config('greencko_user.settings');
    $login_path = $config->get('login.login_path');
    $user_profile = $config->get('user.user_profile');
    $admin_routes = [
      'enabled' => [],
      'disabled' => [],
    ];

    //Add user routes to admin theme
    if ($user_profile) {
      $admin_routes['enabled'][] = 'entity.user.canonical';
      $admin_routes['enabled'][] = 'persistent_login.user_tokens_list';
      $admin_routes['enabled'][] = 'entity.user.edit_form';
    }

    foreach ($collection->all() as $name => $route) {
      if (in_array($name, $admin_routes['enabled'])) {
        $route->setOption('_admin_route', TRUE);
      }
      if (in_array($name, $admin_routes['disabled'])) {
        $route->setOption('_admin_route', FALSE);
      }
    }
    if ($login_path != "") {
      // Change path '/user/login' to '/login'.
      if ($route = $collection->get('user.login')) {
        $route->setPath($login_path);
      }
    }
  }
}
