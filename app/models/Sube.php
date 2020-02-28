<?php

class Sube extends Eloquent {

	protected $table = 'sube';
    public $timestamps = false;

    public function depo()
    {
        return $this->belongsTo('NetsisDepolar')->withTrashed();
    }
}
