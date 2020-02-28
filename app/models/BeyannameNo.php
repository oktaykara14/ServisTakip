<?php

class BeyannameNo extends Eloquent {

	protected $table = 'beyannameno';
    public $timestamps = false;
    public function servis()
    {
        return $this->belongsTo('Servis');
    }

}
