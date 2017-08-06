<?php
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
//
//Route::get('/', function () {
//   return view('welcome');
//});

Route::get('/wechat','WeChatController@index');
Route::get('/qiuniu','WeChatController@qiuniu');
$api = app('Dingo\Api\Routing\Router');
$api->version('v1', function ($api) {
    $api->group(['namespace' => 'App\Api\Controllers'], function ($api) {
        $api->post('notifyUrl', 'WeChatController@notifyUrl');
        $api->get('addTicket', 'TicketController@addTicket');
        $api->get('getPrice', 'TicketController@getPrice');
        $api->get('lessons/{id}', 'LessonsController@show');
        $api->get('token', 'UserTokenController@index');
        $api->get('getBanner', 'BannerController@getBanner');
        $api->get('getLocation', 'BannerController@getLocation');
        $api->post('token', 'UserTokenController@index');
               $api->post('user/login','AuthController@authenticate');
               $api->post('user/register','AuthController@register');
                $api->group(['middleware' => 'jwt.auth'], function ($api) {
                    $api->get('getNotPayTickets', 'TicketController@getNotPayTickets');
                    $api->post('/wechat','WeChatController@index');
                    $api->get('tickets', 'TicketController@getTicketList');
                    $api->POST('delTicket', 'TicketController@delTicket');
                    $api->post('/wechat','WeChatController@index');
                    $api->get('user/me', 'AuthController@getAuthenticatedUser');
                    $api->get('lessons', 'LessonsController@index');
                    $api->post('lessons', 'LessonsController@index');
                    $api->post('addPayer', 'ParyerController@addPayer');
                    $api->post('getno', 'TicketController@getNo');
                    $api->get('payers', 'ParyerController@payerList');
                    $api->post('checkTicket','CheckController@checkTicket');
                    $api->get('checked','CheckController@checkedTickets');

                });

    });
});

/**
 * 管理员路由~
 */


Route::group(['prefix'=>'admin'], function () {
    Route::get('/login','\App\Admin\Controllers\LoginController@index' )->name("login");
    Route::post('/login','\App\Admin\Controllers\LoginController@login' );
    Route::get('/logout','\App\Admin\Controllers\LoginController@logout' )->name('logout');


    Route::group(['middleware'=>'auth:web'], function () {
        //首頁
        Route::get('/home','\App\Admin\Controllers\HomeController@index' );
    });
    /*
     * 用戶模块
     */

    Route::get('/users','\App\Admin\Controllers\UserController@index' );
    Route::get('/users/create','\App\Admin\Controllers\UserController@create' );
    Route::post('/users/store','\App\Admin\Controllers\UserController@store' );
    Route::get('/users/{user}/role','\App\Admin\Controllers\UserController@role' );
    Route::post('/users/{user}/role','\App\Admin\Controllers\UserController@storeRole' );
    /*
     * 角色
     */
    Route::get('/roles','\App\Admin\Controllers\RoleController@index' );
    Route::get('/roles/create','\App\Admin\Controllers\RoleController@create' );
    Route::post('/roles/store','\App\Admin\Controllers\RoleController@store' );
    Route::get('/roles/{role}/permission','\App\Admin\Controllers\RoleController@permission' );
    Route::post('/roles/{role}/permission','\App\Admin\Controllers\RoleController@storePermission' );
    /*
     * 权限
     */
    Route::get('/permissions','\App\Admin\Controllers\PermissionController@index' );
    Route::get('/permissions/create','\App\Admin\Controllers\PermissionController@create' );
    Route::post('/permissions/store','\App\Admin\Controllers\PermissionController@store' );
});