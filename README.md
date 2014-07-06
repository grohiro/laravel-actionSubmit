Laravel ActionSubmit
====================

任意のアクションを実行するための Submit ボタンを生成できます。


```
 <form action="user/list" method="post">

 <!--UserController@search() -->
 <input type="submit" value="Search" name="_action_search" />

 <!--UserController@delete()-->
 <input type="submit" value="Delete" name="_action_delete" />
 </form>
```

一つのフォームに複数の submit ボタンを作成して、押されたボタンによって実行されるアクションを選択します。

**Route**

```
\Route::post('user/list', ['uses' => 'UserController@search', 'as' => 'user.search']);
\Route::post('user/delete', ['uses' => 'UserController@delete', 'as' => 'user.delete'])

```

**View (Blade)**

```
{{Form::open('user/list', ['method' => 'post'])}}
  {{Form::actionSubmitRoute('user.search', 'Search'))}}
  {{Form::actionSubmitRoute('user.delete', 'Delete'))}}
{{Form::close()}}

```

## Submit ボタン
### ルート Route
`Form::actionSubmitRoute($routeName, $value, $patemeters, $attributes)`

|name|type|value|
|----|----|-----|
|routeName|string|'sample.user.list', 'user.batch.delete'|
|value|string|'Search', 'Delete'|
|parameters|array|[route parameters]|
|attributes|array|submit tag attributes|

### アクション Action
`Form::actionSubmitAction($actionName, $value, $parameters, $attributes)`

|name|type|value|
|----|----|-----|
|actionName|string|UserController@search, Sample\UserController@delete|
|value|string|Search, Delete|
|parameters|array|[action parameters]|
|attributes|array|submit tag attributes|


### パス Path
`Form::actionSubmit($path, $value, $attributes)`

|name|type|value|
|----|----|-----|
|path|string|user/serach, user/delete|
|value|string|Search, Delete|
|attributes|array|submit tag attributes|

## Usage
**global.php**

```
\Laravel\Extension\ActionSubmit\ActionSubmit::register();

// Before load filters!
require app_path().'/filters.php';
```

-------------
（また地味なものを作ってしまった・・・。ここからは余談です。）

***どのように実装されているか***

Laravel に取り込まなくても Submit ボタンの onclick で form.action を書き換えれば同じことができるため無用かもしれませんが、JavaScript でガチャガチャやるのがあまりお好きじゃないかた使ってみてください。

任意のアクションをディスパッチするためルータを拡張しています。
Laravel のルータは以下の順序で実行するアクションを解決していました。

+ `\Route::add()` などでルート定義 RouteCollections に登録
+ リクエスト情報から以下の条件を全て満たすルートを取得
  + HTTP メソッドが一致する
  + スキーマ(https?://)が一致する
  + ホスト名が一致する
  + パスが一致する (/controller/action)
+ 一致するルートが見つかれば uses で指定されたアクション (Controller@action) を実行する

ActionSubmit ではルートを取得する条件に Submit ボタンの name に埋め込まれたルートと一致するかどうかの条件を追加しています。

はまりポイントいくつか。


* `App::bind('router', 'MyRouter')` しても `MyRouter` クラスが使われない
  * `Illuminate\Routing\Router` が使われてしまう
  * どうやら `global.php` で `filters.php` がロードされる前に `App::bind()` しないといけない
  * `App:bind()` はどこでも実行できるが、`router` や `request` などは初期化前に実行しておかないと無意味


* `App::bind('router', 'Laravel\Extension\ActionSubmit\Router')` が動かない
  * `router` に拡張したクラスを登録すると全てのURLで404 Not Found になる
  * デバッグするとルート初期登録はできているのに、ディスパッチするときにはルートが空になってる (artisan route は正常)
  * どうやら `app('router')` の呼出で毎回 `Router` インスタンスが生成されているもよう
  * `App:bind('router', 'Hoge', true)` 第3引数の `true` で Singleton にすることで解決


##### See Also

- Illuminate\Routing\Router
- Illuminate\Routing\Route
- Illuminate\Routing\RouteCollections

