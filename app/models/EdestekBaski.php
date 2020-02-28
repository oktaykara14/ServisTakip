<?php

class EdestekBaski extends Eloquent {

	protected $table = 'edestekbaski';
    public $timestamps = false;

    public function edestekmusteri()
    {
        return $this->belongsTo('EdestekMusteri')->withTrashed();
    }

    public function edestekkartbaski()
    {
        return $this->belongsTo('EdestekKartBaski');
    }

    public function edestekplasiyer()
    {
        return $this->belongsTo('Plasiyer');
    }

    public function edestekpersonel()
    {
        return $this->belongsTo('EdestekPersonel')->withTrashed();
    }
}
