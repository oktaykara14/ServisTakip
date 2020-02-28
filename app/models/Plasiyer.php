<?php

class Plasiyer extends Eloquent {

	protected $table = 'plasiyer';
        public $timestamps = false;

    public function edestekbaski()
    {
        return $this->hasMany('EdestekBaski');
    }

    public function edesteksistembilgi()
    {
        return $this->belongsTo('EdestekSistemBilgi');
    }
}
