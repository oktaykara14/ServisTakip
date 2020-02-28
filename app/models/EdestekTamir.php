<?php

class EdestekTamir extends Eloquent {

	protected $table = 'edestektamir';
        public $timestamps = false;

    public function edestekmusteri()
    {
        return $this->belongsTo('EdestekMusteri')->withTrashed();
    }

    public function edestektamirurun()
    {
        return $this->belongsTo('EdestekTamirUrun');
    }

    public function edestektamirislem()
    {
        return $this->belongsTo('EdestekTamirIslem');
    }


    public function edestekpersonel()
    {
        return $this->belongsTo('EdestekPersonel')->withTrashed();
    }
}
