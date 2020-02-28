<?php

class Periyodik extends Eloquent {
	protected $table = 'periyodik';
    //public $timestamps = false;

    public function depogelen()
    {
        return $this->belongsTo('DepoGelen');
    }

}
