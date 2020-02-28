<?php

use Illuminate\Http\RedirectResponse;

class RemindersController extends Controller {

	/**
	 * Display the password reminder view.
	 *
	 * @return Response
	 */
	public function getRemind()
	{
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return View::make('password.remind')->with('title', 'Şifremi Unuttum!');
	}

    /**
     * Handle a POST request to remind a user of their password.
     *
     * @return RedirectResponse
     */
	public function postRemind()
	{
        $rules =  array('captcha' => array('required', 'captcha'),'email' => array('required', 'email'));
        $validate = Validator::make(Input::all(),$rules);
        if ($validate->fails()) {
            $messages = $validate->messages();
            Input::flash();
            /** @noinspection PhpIncompatibleReturnTypeInspection */
            return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Doğrulama Hatası', 'text' => '' . $messages->first(). '', 'type' => 'error'));
        }
        $kullanici=Kullanici::where('email',Input::get('email'))->first();
        if($kullanici){
            switch ($response = Password::remind(Input::only(array('email')), function($message)
            {
                $message->subject('Servis Takip Şifre Sıfırlama');

            }))
            {
                case Password::INVALID_USER:
                    Input::flash();
                    /** @noinspection PhpIncompatibleReturnTypeInspection */
                    return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Email Hatası', 'text' => 'Bu Email Adresine Ait Kullanıcı Bulunamadı!', 'type' => 'error'));
                case Password::REMINDER_SENT:
                    /** @noinspection PhpIncompatibleReturnTypeInspection */
                    return Redirect::to('login')->with(array('mesaj' => 'true', 'title' => 'Hatırlatma Gönderildi', 'text' => 'Girilen Mail Adresine Hatırlatma Maili Gönderildi', 'type' => 'success'));
            }
            /** @noinspection PhpIncompatibleReturnTypeInspection */
            return Redirect::to('login')->with(array('mesaj' => 'true', 'title' => 'Hatırlatma Gönderildi', 'text' => 'Girilen Mail Adresine Hatırlatma Maili Gönderildi', 'type' => 'success'));
        }else{
            Input::flash();
            /** @noinspection PhpIncompatibleReturnTypeInspection */
            return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'Email Hatası', 'text' => 'Bu Email Adresine Ait Kullanıcı Bulunamadı!', 'type' => 'error'));
        }
	}

	/**
	 * Display the password reset view for the given token.
	 *
	 * @param  string  $token
	 * @return Response
	 */
	public function getReset($token = null)
	{
		if (is_null($token)) App::abort(404);
        $passwordreset=PasswordReset::where('token',$token)->first();
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return View::make('password.reset')->with(array('email'=>$passwordreset->email,'token'=>$token,'title'=>'Şifre Sıfırlama'));
	}

    /**
     * Handle a POST request to reset a user's password.
     *
     * @return RedirectResponse
     */
	public function postReset()
	{
		$credentials = Input::only(array('email', 'password', 'password_confirmation', 'token')
		);

		$response = Password::reset($credentials, function($user, $password)
		{
			$user->password = Hash::make($password);

			$user->save();
		});

		switch ($response)
		{
			case Password::INVALID_PASSWORD:
			case Password::INVALID_TOKEN:
			case Password::INVALID_USER:
            Input::flash();
                /** @noinspection PhpIncompatibleReturnTypeInspection */
                return Redirect::back()->withInput()->with(array('mesaj' => 'true', 'title' => 'İşlem İptal Edildi!', 'text' => Lang::get($response), 'type' => 'error'));

            case Password::PASSWORD_RESET:
                /** @noinspection PhpIncompatibleReturnTypeInspection */
                return Redirect::to('login')->with(array('mesaj' => 'true', 'title' => 'Şifreniz Değiştirildi.', 'text' => 'Yeni Şifreniz ile Giriş Yapabilirsiniz.', 'type' => 'success'));
        }
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return Redirect::to('login')->with(array('mesaj' => 'true', 'title' => 'Şifreniz Değiştirildi.', 'text' => 'Yeni Şifreniz ile Giriş Yapabilirsiniz.', 'type' => 'success'));
    }

}
