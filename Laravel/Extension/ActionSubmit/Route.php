<?php
namespace Laravel\Extension\ActionSubmit;

use Illuminate\Http\Request;
use Illuminate\Routing\Matching\UriValidator;
use Illuminate\Routing\Matching\HostValidator;
use Illuminate\Routing\Matching\MethodValidator;
use Illuminate\Routing\Matching\SchemeValidator;

/**
 * actionSubmit() を使うためのRouteクラス.
 *
 */
class Route extends \Illuminate\Routing\Route
{
  static $actionSubmitValidators;

  public function __construct($methods, $uri, $action)
  {
    // actionSubmit は UriValidator を使わない.
    static::$actionSubmitValidators = [
        new MethodValidator, new SchemeValidator,
        new HostValidator,
        new ActionSubmitValidator(),
    ];

    parent::__construct($methods, $uri, $action);
  }

  /**
   * Determine if the route matches given request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  bool  $includingMethod
   * @return bool
   */
  public function matches(Request $request, $includingMethod = true)
  {
    $this->compileRoute();

    if ($this->isActionSubmitRequest($request)) {
      $validators = static::$actionSubmitValidators;
    } else {
      $validators = $this->getValidators();
    }

    foreach ($validators as $validator)
    {
      if ( ! $includingMethod && $validator instanceof MethodValidator) continue;

      if ( ! $validator->matches($this, $request)) return false;
    }

    return true;
  }

  /**
   * Form::actionSubmit() を使ったリクエストなら true を返す.
   *
   * @return
   */
  protected function isActionSubmitRequest(Request $request)
  {
    $actionSubmit = false;
    $all = $request->all();
    foreach (array_keys($all) as $key) {
      if (preg_match("/^_action_(.*)$/", $key, $matches)) {
        $actionSubmit = true;
        break;
      }
    }

    return $actionSubmit;
  }
}

/**
 * Form::actionSubmit() のバリデータ.
 */
class ActionSubmitValidator extends \Illuminate\Routing\Matching\UriValidator
{
  public function matches(\Illuminate\Routing\Route $route, Request $request)
  {
    $all = $request->all();
    foreach (array_keys($all) as $key) {
      if (preg_match("/^_action_(.*)$/", $key, $matches)) {
        break;
      }
    }

    $url = str_replace('_', '/', $matches[1]);
    $path = $url == '/' ? '/' : '/'.$url;
    $regex = $route->getCompiled()->getRegex();
    return preg_match($regex, rawurldecode($path));
  }
}


