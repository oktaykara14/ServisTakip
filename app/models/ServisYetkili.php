<?php

class ServisYetkili extends Eloquent {

	protected $table = 'servisyetkili';
        public $timestamps = false;

    public function kullanici()
    {
        return $this->belongsTo('Kullanici');
    }
}
