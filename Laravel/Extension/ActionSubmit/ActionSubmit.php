<?php
namespace Laravel\Extension\ActionSubmit;

/**
 * 指定のアクションを実行する submit ボタンを生成します.
 * 一つのフォームで複数のアクションに送信できます.
 *
 * Form::actionSubmit()
 * <pre>
 * <form action="/user/list">
 * {{\Form::submit('検索')}} // UserController@search()
 * 
 * // dispatch UserController@delete() action
 * {{\Form::actionSubmitRoute('sample.user.delete', '削除')}} // 
 * {{\Form::actionSubmitAction('UserController@delete', '削除')}}
 * </form>
 * <pre>
 */
class ActionSubmit
{
  public static function register() {

    \App::bind('router', '\Laravel\Extension\ActionSubmit\Router', true);

    /**
     * 指定アクションへサブミットする(URL指定).
     * Form::actionSubmit('sample/user/list/download', 'ダウンロード');
     */
    \Form::macro('actionSubmit', function($to, $value, $attributes = []) {
      $attributes['name'] = "_action_".str_replace('/', '_', $to);
      return \Form::submit($value, $attributes);
    });

    /**
     * 指定アクションへサブミットする(ルート名指定).
     * Form::actionSubmitRoute('sample.user.list.download', 'ダウンロード');
     */
    \Form::macro('actionSubmitRoute', function($route, $value = '', $patemeters = [], $attributes = []) {
      $action = app('url')->route($route, $parameters, false);
      $attributes['name'] = "_action".str_replace('/', '_', $action);
      return \Form::submit($value, $attributes);
    });

    /**
     * 指定アクションへサブミットする(アクション指定).
     * Form::actionSubmitAction('Sample\UserController@batchdownload', 'ダウンロード');
     */
    \Form::macro('actionSubmitAction', function($action, $value = '', $patemeters = [], $attributes = []) {
      $target = app('url')->action($action, $parameters, false);
      $attributes['name'] = "_action".str_replace('/', '_', $target);
      return \Form::submit($value, $attributes);
    });
  }
}


