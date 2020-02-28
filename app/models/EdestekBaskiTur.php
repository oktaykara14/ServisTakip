<?php

class EdestekBaskiTur extends Eloquent {

	protected $table = 'edestekbaskitur';
        public $timestamps = false;

    public function edestekkartbaski()
    {
        return $this->hasMany('EdestekKartBaski');
    }
}
