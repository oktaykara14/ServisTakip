<?php

class EdestekIslem extends Eloquent {

	protected $table = 'edestekislem';
        public $timestamps = false;
        public function sonislem()
        {
            return $this->hasMany('EdestekPersonel');
        }
        
        public function edestekpersonel()
        {
            return $this->belongsTo('EdestekPersonel');
        }

        public function edestekmusteri()
        {
            return $this->belongsTo('EdestekMusteri');
        }

        public function edestekkonu()
        {
            return $this->belongsTo('EdestekKonu');
        }

        public function edestekkonuislem()
        {
            return $this->belongsTo('EdestekKonuIslem');
        }
}
