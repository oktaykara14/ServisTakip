<?php

class Beyanname extends Eloquent {

	protected $table = 'beyanname';
    //public $timestamps = false;
    public function servis()
    {
        return $this->belongsTo('Servis');
    }
    public function kullanici()
    {
        return $this->belongsTo('Kullanici');
    }

}
