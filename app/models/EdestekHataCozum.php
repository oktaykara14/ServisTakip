<?php

class EdestekHataCozum extends Eloquent {

	protected $table = 'edestekhatacozum';
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
        
        public function guncelleyen($id)
        {
            $personel = EdestekPersonel::find($id)->withTrashed();
            return $personel;
        }
}
