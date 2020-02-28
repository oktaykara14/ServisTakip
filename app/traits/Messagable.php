<?php namespace Traits;
use Alici;
use Ileti;
use Illuminate\Database\Eloquent\Relations\belongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Mesaj;

trait Messagable
{
    /**
     * Message relationship
     *
     * @return HasMany
     */
    public function mesaj()
    {
        return $this->hasMany('Mesaj');
    }

    /**
     * Thread relationship
     *
     * @return belongsToMany
     */
    public function ileti()
    {
        return $this->belongsToMany('Ileti', 'alici');
    }

    /**
     * Returns the new messages count for user
     *
     * @return int
     */
    public function newMessagesCount()
    {
        $threadsWithNewMessages = 0;
        $alicilar = Alici::where('kullanici_id', $this->id)->lists('son_okuma', 'ileti_id');

        if ($alicilar) {
            $iletiler = Ileti::whereIn('id', array_keys($alicilar))->get();

            foreach ($iletiler as $ileti) {
                if ($ileti->updated_at > $alicilar[$ileti->id]) {
                    $threadsWithNewMessages += Mesaj::where('kullanici_id','<>',$this->id)->where('updated_at','>',$alicilar[$ileti->id])->get()->count();
                }
            }
        }

        return $threadsWithNewMessages;
    }

    /**
     * Returns all threads with new messages
     *
     * @return array
     */
    public function threadsWithNewMessages()
    {
        $threadsWithNewMessages = 0;
        $alicilar = Alici::where('kullanici_id', $this->id)->lists('son_okuma', 'ileti_id');

        if ($alicilar) {
            $iletiler = Ileti::whereIn('id', array_keys($alicilar))->get();

            foreach ($iletiler as $ileti) {
                if ($ileti->updated_at > $alicilar[$ileti->id]) {
                    $threadsWithNewMessages += Mesaj::where('kullanici_id','<>',$this->id)->where('updated_at','>',$alicilar[$ileti->id])->get()->count();
                }
            }
        }

        return $threadsWithNewMessages;
    }
}
