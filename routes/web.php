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

Route::get('/', function () {
   return view('welcome');
});

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
                });

    });
});