<?php

class Depolararasi extends Eloquent {

	protected $table = 'depolararasi';
        public $timestamps = false;
    public function uretimyer()
    {
        return $this->belongsTo('UretimYer');
    }

    public function kullanici()
    {
        return $this->belongsTo('Kullanici');
    }


}
