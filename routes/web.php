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


Auth::routes();

// 右上のロゴを押すときのあれ
Route::get('/', function () {
    return view('welcome');
});


Route::get('/home', 'HomeController@index')->name('home');

Route::group(['prefix' => 'admin', 'middleware'=>['auth', 'can:admin-higher'], 'namespace' => 'Admin', 'as' => 'admin::'], function(){
    Route::get('zonecodes', 'ZonecodesController@index')->name('zonecodes');
    Route::post('zonecodes/upload', 'ZonecodesController@upload')->name('zonecodes.upload');
    Route::get('zonecodes/download', 'ZonecodesController@download')->name('zonecodes.download');

    Route::get('skutransfers', 'SkutransfersController@index')->name('skutransfers');
    Route::post('skutransfers/upload', 'SkutransfersController@upload')->name('skutransfers.upload');
    Route::get('skutransfers/download', 'SkutransfersController@download')->name('skutransfers.download');

    Route::get('ordersheets', 'OrdersheetsController@index')->name('ordersheets');
    Route::post('ordersheets/upload', 'OrdersheetsController@upload')->name('ordersheets.upload');
    Route::get('ordersheets/download', 'OrdersheetsController@download')->name('ordersheets.download');
    Route::get('ordersheets/deletelines', 'OrdersheetsController@deletelines')->name('ordersheets.deletelines');
});


Route::group(['prefix' => '', 'middleware'=>['auth', 'can:user-higher'], 'namespace' => 'Work', 'as' => 'work::'], function(){
    Route::get('/work', 'WorkController@index')->name('work');
    Route::get('/work/renew_box', 'WorkController@renew_box')->name('work.renew_box');
    Route::get('/work/delete_last_line', 'WorkController@delete_last_line')->name('work.delete_last_line');

    Route::get('/search_result', 'SearchResultController@index')->name('search_result');
    Route::get('/search_result/deal_item', 'SearchResultController@deal_item')->name('search_result.deal_item');
    Route::get('/search_result/put_into_box', 'SearchResultController@put_into_box')->name('search_result.put_into_box');

    Route::get('/print', 'PrintController@index')->name('print');
    Route::get('/print/print', 'PrintController@print')->name('print.print');
    Route::get('/single_print', 'PrintController@single_index')->name('print.single_index');
    Route::get('/single_print/addition', 'PrintController@addition')->name('print.addition');
    Route::get('/single_print/addition_clear', 'PrintController@addition_clear')->name('print.addition_clear');
    Route::get('/single_print/print', 'PrintController@single_print')->name('print.single_print');

    Route::get('/item_search', 'ItemSearchController@index')->name('item_search');
    Route::get('/item_search/search', 'ItemSearchController@search')->name('item_search.search');
});

Route::group(['prefix' => 'order_receive', 'middleware'=>['auth', 'can:user-higher'], 'namespace' => 'OrderReceive', 'as' => 'order_receive::'], function(){
    Route::get('info', 'StockOrderController@index')->name('info');


    Route::get('itemrelations/index', 'ItemrelationsController@index')->name('itemrelations.index');
    Route::post('itemrelations/upload', 'ItemrelationsController@upload')->name('itemrelations.upload');
    Route::get('itemrelations/download', 'ItemrelationsController@download')->name('itemrelations.download');


    Route::get('itemborders/index', 'ItembordersController@index')->name('itemborders.index');
    Route::post('itemborders/upload', 'ItembordersController@upload')->name('itemborders.upload');
    Route::get('itemborders/download', 'ItembordersController@download')->name('itemborders.download');

    Route::get('orderdocuments/index', 'OrderdocumentsController@index')->name('orderdocuments.index');
    Route::get('orderdocuments/select', 'OrderdocumentsController@select')->name('orderdocuments.select');
    Route::get('orderdocuments/edit', 'OrderdocumentsController@edit')->name('orderdocuments.edit');
    Route::post('orderdocuments/upload', 'OrderdocumentsController@upload')->name('orderdocuments.upload');
    Route::get('orderdocuments/download', 'OrderdocumentsController@download')->name('orderdocuments.download');
    Route::get('orderdocuments/detail', 'OrderdocumentsController@detail')->name('orderdocuments.detail');

    Route::get('receive/index', 'ReceiveController@index')->name('receive.index');
    Route::get('receive/research', 'ReceiveController@research')->name('receive.research');
    Route::get('receive/detail', 'ReceiveController@detail')->name('receive.detail');
    Route::get('receive/receive', 'ReceiveController@receive')->name('receive.receive');

    Route::get('stockitems/index', 'StockitemsController@index')->name('stockitems.index');
    Route::get('stockitems/all_renew', 'StockitemsController@all_renew')->name('stockitems.all_renew');
    Route::get('stockitems/select', 'StockitemsController@select')->name('stockitems.select');
    Route::get('stockitems/edit', 'StockitemsController@edit')->name('stockitems.edit');
    Route::post('stockitems/upload', 'StockitemsController@upload')->name('stockitems.upload');
    Route::get('stockitems/download', 'StockitemsController@download')->name('stockitems.download');
});

Route::group(['prefix' => 'stock_work', 'middleware'=>['auth', 'can:user-higher'], 'namespace' => 'Stock_work', 'as' => 'stock_work::'], function(){
    Route::get('work', 'WorkController@index')->name('work');
    Route::get('work/deal_and_recommend', 'WorkController@deal_and_recommend')->name('work.deal_and_recommend');
    Route::get('work/print', 'WorkController@print')->name('work.print');
    Route::get('work/renew_box', 'WorkController@renew_box')->name('work.renew_box');

    Route::get('work_by_line', 'WorkController@work_by_line')->name('work_by_line');

});
