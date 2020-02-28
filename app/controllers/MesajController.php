<?php
//transaction tamamlandı
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;

class MesajController extends BaseController
{
    /**
     * Show all of the message threads to the user
     *
     * @return mixed
     */
    public function getIndex()
    {
        $kullaniciid = Auth::user()->id;

        // All threads, ignore deleted/archived participants
        //$iletiler = Ileti::getAllLatest()->get();

        // All threads that user is participating in
        $iletiler = Ileti::forUser($kullaniciid)->latest('updated_at')->get();
        foreach($iletiler as $ileti){
            $ileti->alici=Alici::where('ileti_id',$ileti->id)->where('kullanici_id','<>',$kullaniciid)->first();
        }

        // All threads that user is participating in, with new messages
        // $threads = Thread::forUserWithNewMessages($currentUserId)->latest('updated_at')->get();

        return View::make('mesaj.index', compact('iletiler', 'kullaniciid'))->with(array('title'=>'Mesajlaşma Ekranı'));

    }

    /**
     * Shows a message thread
     *
     * @param $id
     * @return mixed
     */
    public function getDetay($id)
    {
        try {
            $ileti = Ileti::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            Session::flash('error_message', $id.' numaralı İleti bulunamadı');

            return Redirect::to('mesaj');
        }

        // show current user in list if not a current participant
        // $users = User::whereNotIn('id', $thread->participantsUserIds())->get();

        // don't show the current user in list
        $kullaniciid = Auth::user()->id;
        $kullanicilar = Kullanici::whereNotIn('id', $ileti->participantsUserIds($kullaniciid))->get();

        $ileti->markAsRead($kullaniciid);

        return View::make('mesaj.goster', compact('ileti', 'kullanicilar'))->with(array('title'=>'Mesajlaşma Detayı'));
    }

    /**
     * Creates a new message thread
     *
     * @return mixed
     */
    public function ekle()
    {
        $kullanicilar = Kullanici::where('id', '!=', Auth::id())->get();

        return View::make('mesaj.ekle', compact('kullanicilar'))->with(array('title'=>'Yeni Mesaj Ekle'));

    }

    /**
     * Stores a new message thread
     *
     * @return mixed
     */
    public function kaydet()
    {
        $input = Input::all();

        $ileti = Ileti::create(
            [
                'konu' => $input['konu'],
            ]
        );

        // Message
        Mesaj::create(
            [
                'ileti_id'     => $ileti->id,
                'kullanici_id'  => Auth::user()->id,
                'icerik'          => $input['mesaj'],
            ]
        );

        // Sender
        Alici::create(
            [
                'ileti_id'     => $ileti->id,
                'kullanici_id'  => Auth::user()->id,
                'son_okuma'     => new Carbon
            ]
        );

        // Recipients
        if (Input::has('alicilar')) {
            $ileti->addParticipants($input['alicilar']);
        }

        return Redirect::to('mesaj')->with(array('mesaj' => 'true', 'title' => 'Yeni Sohbet Eklendi', 'text' => 'Yeni Bir Sohbet Grubu Eklendi.', 'type' => 'success'));
    }

    /**
     * Adds a new message to a current thread
     *
     * @param $id
     * @return mixed
     */
    public function postGuncelle($id)
    {
        try {
            $ileti = Ileti::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            Session::flash('error_message', $id.' numaralı İleti bulunamadı');

            return Redirect::to('mesaj');
        }

        $ileti->activateAllParticipants();

        // Message
        Mesaj::create(
            [
                'ileti_id'      => $ileti->id,
                'kullanici_id'   => Auth::id(),
                'icerik'           => Input::get('mesaj'),
            ]
        );

        // Add replier as a participant
        $alici = Alici::firstOrCreate(
            [
                'ileti_id'     => $ileti->id,
                'kullanici_id'  => Auth::user()->id
            ]
        );
        $alici->son_okuma = new Carbon;
        $alici->save();

        // Recipients
        if (Input::has('alicilar')) {
            $ileti->addParticipants(Input::get('alicilar'));
        }

        return Redirect::to('mesaj/detay/' . $id)->with(array('mesaj' => 'true', 'title' => 'Sohbet Bilgisi Güncellendi', 'text' => 'Sohbet Bilgisi Güncellendi.', 'type' => 'success'));

    }
}
