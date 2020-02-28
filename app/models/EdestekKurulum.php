<?php

class EdestekKurulum extends Eloquent {

	protected $table = 'edestekkurulum';
        public $timestamps = false;
        
        public function edestekmusteri()
        {
            return $this->belongsTo('EdestekMusteri')->withTrashed();
        }
        
        public function edestekkurulumtur()
        {
            return $this->belongsTo('EdestekKurulumTur');
        }
        
        public function edestekpersonel()
        {
            return $this->belongsTo('EdestekPersonel')->withTrashed();
        }

}
