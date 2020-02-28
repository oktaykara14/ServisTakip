<?php

Route::get('/', function(){ return Redirect::to('index');});
Route::controller('login','LoginController');
Route::get('logout', 'LoginController@logout');
Route::get('yetkisiz', 'LoginController@yetkisiz');
Route::get('info','LoginController@info');
Route::get('musterionay/{id}/{secilenler}','UcretlendirmeController@getMusterionay');
Route::get('musterionay/{id}','UcretlendirmeController@getMusterionay');
Route::post('musterionay','UcretlendirmeController@postMusterionay');
Route::post('ucretlendirme/musterionaylist','UcretlendirmeController@postMusterionaylist');
Route::post('musterireddet','UcretlendirmeController@postMusterireddet');
Route::get('bakim','LoginController@bakim');
Route::post('uruntakip', 'LoginController@postUruntakip');
Route::controller('reminder','RemindersController');

Route::group(array('before' => 'auth'), function()
{
    Route::controller('lock', 'LockController');
    Route::controller('profil','ProfilController');
    Route::controller('mesaj', 'MesajController');
    Route::controller('backend','BackendController');

    Route::group(array('before' => 'suservis'), function()
    {
        Route::controller('suservis','SuservisController');
        Route::controller('isiservis','IsiservisController');      
    });

    Route::group(array('before' => 'elkservis'), function()
    {
        Route::controller('elkservis','ElkservisController');
    });

    Route::group(array('before' => 'gazservis'), function()
    {
        Route::controller('gazservis','GazservisController');
    });

    Route::group(array('before' => 'mekanikgaz'), function()
    {
        Route::controller('mekanikgaz','GazservismekanikController');
    });

    Route::group(array('before' => 'uretim'), function()
    {
        Route::controller('uretim','UretimController');
    });

    Route::group(array('before' => 'destek'), function()
    {
        Route::controller('edestek','EdestekController');
        Route::controller('destek','DestekController');
    });

    Route::group(array('before' => 'kalibrasyon'), function()
    {
        Route::controller('kalibrasyon','KalibrasyonController');
    });

    Route::group(array('before' => 'admin'), function()
    {
        Route::controller('digerdatabase','DigerdatabaseController');
        Route::controller('kullanicilar','KullaniciController');
    });

    Route::group(array('before' => 'yetkili'), function()
    {
        Route::controller('sudatabase','SudatabaseController');
        Route::controller('elkdatabase','ElkdatabaseController');
        Route::controller('gazdatabase','GazdatabaseController');
        Route::controller('isidatabase','IsidatabaseController');
        Route::controller('mekanikdatabase','MekanikdatabaseController');
        Route::controller('destekdatabase','DestekdatabaseController');
    });
    
    Route::group(array('before' => 'ortak'), function()
    {
        Route::controller('index','MainController');
        Route::controller('main','MainController');
        Route::controller('servistakip','ServistakipController');

    });

    Route::group(array('before' => 'depo'), function()
    {
        Route::controller('depo','DepoController');
        Route::controller('uretim','UretimController');
    });

    Route::group(array('before' => 'ucretlendirme'), function()
    {
        Route::controller('ucretlendirme','UcretlendirmeController');
    });

    Route::group(array('before' => 'rapor'), function()
    {
        Route::controller('rapor','RaporController');
    });

    Route::group(array('before' => 'sube'), function()
    {
        Route::controller('sube','SubeController');
        Route::controller('subedatabase','SubedatabaseController');
    });

    Route::group(array('before' => 'abone'), function()
    {
        Route::controller('abone','AboneController');
    });
});
