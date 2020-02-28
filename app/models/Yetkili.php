<?php

class Yetkili extends Eloquent {

	protected $table = 'yetkili';
        public $timestamps = false;

    public function netsiscari()
    {
        return $this->belongsTo('NetsisCari');
    }
    public function kullanici()
    {
        return $this->belongsTo('Kullanici');
    }
}
