<?php
namespace Laravel\Extension\ActionSubmit;

use Illuminate\Http\Request;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;

/**
 * actionSubmit() を使うためのRouterクラス.
 *
 */
class Router extends \Illuminate\Routing\Router
{
	public function __construct(Dispatcher $events, Container $container = null)
  {
    parent::__construct($events, $container);
  }

	protected function newRoute($methods, $uri, $action)
	{
		return new Route($methods, $uri, $action);
	}
}

