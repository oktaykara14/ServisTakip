<?php

/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
| Below you will find the "before" and "after" events for the application
| which may be used to do any work before or after a request into your
| application. Here you may also register your custom route filters.
|
*/

App::before( function( $request )
{
    /*if( ! Request::secure() )
    {
        return Redirect::secure( Request::path() );
    }*/
});


App::after(function($request, $response)
{
	//
});

/*
|--------------------------------------------------------------------------
| Authentication Filters
|--------------------------------------------------------------------------
|
| The following filters are used to verify that the user of the current
| session is logged into this application. The "basic" filter easily
| integrates HTTP Basic authentication for quick, simple checking.
|
*/

Route::filter('auth', function()
{
    //Debugbar::enable();
	if (Auth::guest())
	{
		if (Request::ajax())
		{
			return Response::make('Unauthorized', 401);
		}
		else
		{
			return Redirect::guest('login');
		}
	}else{
        $bakim = BackendController::getBakimDurum();
        if($bakim){
            return Redirect::to('bakim');
        }
        if(!Request::has('sEcho') && !Request::has('ireport') && !Request::has('draw')){
            BackendController::KullaniciBilgisi(Auth::user()->id);
            Kullanici::find(Auth::user()->id)->update(['last_active'=>date('Y-m-d H:i:s')]);
        }
	}

});


Route::filter('auth.basic', function()
{
	return Auth::basic();
});

/*
|--------------------------------------------------------------------------
| Guest Filter
|--------------------------------------------------------------------------
|
| The "guest" filter is the counterpart of the authentication filters as
| it simply checks that the current user is not logged in. A redirect
| response will be issued if they are, which you may freely change.
|
*/

Route::filter('guest', function()
{
	if (Auth::check()) return Redirect::to('/');
});

/*
|--------------------------------------------------------------------------
| CSRF Protection Filter
|--------------------------------------------------------------------------
|
| The CSRF filter is responsible for protecting your application against
| cross-site request forgery attacks. If this special token in a user
| session does not match the one given in this request, we'll bail.
|
*/

Route::filter('csrf', function()
{
	if ( Session::token() !== Input::get('_token'))
	{
		throw new Illuminate\Session\TokenMismatchException;
	}
});

Route::filter('admin', function()
{
	if (Auth::user()->grup_id >5 )
	{
		return Redirect::to('yetkisiz');
	}
});

Route::filter('suservis', function()
{
	if (Auth::user()->grup_id>6  && Auth::user()->grup_id!=7 && Auth::user()->grup_id!=11 && Auth::user()->grup_id!=16)
	{
		return Redirect::to('yetkisiz');
	}
});

Route::filter('elkservis', function()
{
	if (Auth::user()->grup_id>6 && Auth::user()->grup_id!=8 && Auth::user()->grup_id!=12 && Auth::user()->grup_id!=16)
	{
		return Redirect::to('yetkisiz');
	}
});

Route::filter('gazservis', function()
{
	if (Auth::user()->grup_id>6 && Auth::user()->grup_id!=7 && Auth::user()->grup_id!=11 && Auth::user()->grup_id!=16)
	{
		return Redirect::to('yetkisiz');
	}
});

Route::filter('mekanikgaz', function()
{
    if (Auth::user()->grup_id>6 && Auth::user()->grup_id!=9 && Auth::user()->grup_id!=16)
    {
        return Redirect::to('yetkisiz');
    }
});

Route::filter('uretim', function()
{
    if (Auth::user()->grup_id>6 && Auth::user()->grup_id!=14 && Auth::user()->grup_id!=15  && Auth::user()->grup_id!=16)
    {
        return Redirect::to('yetkisiz');
    }
});


Route::filter('destek', function()
{
	if (Auth::user()->grup_id>4 && Auth::user()->grup_id!=10)
	{
		return Redirect::to('yetkisiz');
	}
});

Route::filter('kalibrasyon', function()
{
    if (Auth::user()->grup_id>5 && Auth::user()->grup_id!=9 && Auth::user()->grup_id!=13 )
    {
        return Redirect::to('yetkisiz');
    }
});

Route::filter('yetkili', function()
{
    if (Auth::user()->grup_id>9 )
    {
        return Redirect::to('yetkisiz');
    }
});

Route::filter('ortak', function()
{
	if ( Auth::user()->grup_id==10 )
	{
		return Redirect::to('yetkisiz');
	}
});


Route::filter('ucretlendirme', function()
{
    if ( Auth::user()->grup_id==10 || Auth::user()->grup_id==13 || Auth::user()->grup_id==16 || Auth::user()->grup_id==18 )
    {
        return Redirect::to('yetkisiz');
    }
});


Route::filter('depo', function()
{
    if ( Auth::user()->grup_id==6 || Auth::user()->grup_id==13 || Auth::user()->grup_id==10 || Auth::user()->grup_id==18 || Auth::user()->grup_id==19 )
    {
        return Redirect::to('yetkisiz');
    }
});

Route::filter('sube', function()
{
    if (Auth::user()->grup_id>6 && Auth::user()->grup_id!=17 && Auth::user()->grup_id!=18 )
    {
        return Redirect::to('yetkisiz');
    }
});

Route::filter('abone', function()
{
    if (Auth::user()->grup_id>4 && Auth::user()->grup_id!=19 )
    {
        return Redirect::to('yetkisiz');
    }
});

Route::filter('rapor', function()
{
    if (Auth::user()->grup_id>18 )
    {
        return Redirect::to('yetkisiz');
    }
});
