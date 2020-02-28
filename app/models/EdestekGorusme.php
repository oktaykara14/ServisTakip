<?php

class EdestekGorusme extends Eloquent {

	protected $table = 'edestekgorusme';
        public $timestamps = false;

    public function edestekmusteri()
    {
        return $this->belongsTo('EdestekMusteri')->withTrashed();
    }

    public function edestekkonu()
    {
        return $this->belongsTo('EdestekKonu');
    }

    public function edestekkonudetay()
    {
        return $this->belongsTo('EdestekKonuDetay');
    }

    public function edestekpersonel()
    {
        return $this->belongsTo('EdestekPersonel')->withTrashed();
    }
}
