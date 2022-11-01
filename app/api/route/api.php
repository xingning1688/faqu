<?php
use think\facade\Route;

//测试用
Route::get('test','app\api\controller\Login@test')/*->name('test')*/;//测试用
Route::get('code','app\api\controller\Login@getCode')/*->name('test')*/;  //测试用
Route::get('code2Session','app\api\controller\Login@code2Session')/*->name('test')*/;//测试用

Route::get('test2','app\api\controller\Common@fabuConfig')/*->name('test')*/;//测试用
//Route::get('test2','app\api\controller\LawyerCase@detail')/*->name('test')*/;//测试用
Route::get('leave_message_add','app\api\controller\LeaveMessage@add');


//正式
Route::post('code2Session','app\api\controller\Login@code2Session');
Route::post('login','app\api\controller\Login@login');
Route::post('leave_message_add','app\api\controller\LeaveMessage@add');
Route::get('fabuConfig','app\api\controller\Common@fabuConfig');
Route::get('fabuConfig2','app\api\controller\Common@fabuConfig2');
Route::post('send_email','app\api\controller\Email@sendEmail');
Route::get('buy_contract','app\api\controller\OrderContract@buyContract');









