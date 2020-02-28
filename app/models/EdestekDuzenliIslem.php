<?php

class EdestekDuzenliIslem extends Eloquent {

	protected $table = 'edestekduzenliislem';
        public $timestamps = false;


    public function edestekpersonel()
    {
        return $this->belongsTo('EdestekPersonel')->withTrashed();
    }

    public function edestekkonu()
    {
        return $this->belongsTo('EdestekKonu');
    }

    public function edestekkonuislem()
    {
        return $this->belongsTo('EdestekKonuIslem');
    }

    public function edestekmusteri()
    {
        return $this->belongsTo('EdestekMusteri')->withTrashed();
    }

    public static function aralikkatsayi($araliktip)
    {
        switch ($araliktip) {
            case 1: //gun
                return 1;
            case 2: //hafta
                return 7;
            case 3: //ay
                return 30;
            case 4: //yıl
                return 365;
            default:
                return 1;
        }
    }
}
