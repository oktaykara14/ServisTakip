<?php

class EdestekKartBaski extends Eloquent {

	protected $table = 'edestekkartbaski';
        public $timestamps = false;

    public function edestekbaski()
    {
        return $this->hasMany('EdestekBaski');
    }

    public function edestekbaskitur()
    {
        return $this->belongsTo('EdestekBaskiTur');
    }
}
