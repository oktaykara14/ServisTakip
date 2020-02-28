<?php

class Ucretlendirilen extends Eloquent {

	protected $table = 'ucretlendirilen';
        public $timestamps = false;
    public function uretimyer()
    {
        return $this->belongsTo('UretimYer');
    }

    public function parabirimi()
    {
        return $this->belongsTo('ParaBirimi');
    }
    public function parabirimi2()
    {
        return $this->belongsTo('ParaBirimi2');
    }
    public function kullanici()
    {
        return $this->belongsTo('Kullanici');
    }


}
