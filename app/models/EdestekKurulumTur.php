<?php

class EdestekKurulumTur extends Eloquent {

	protected $table = 'edestekkurulumtur';
        public $timestamps = false;

        public function edestekkurulum()
        {
            return $this->hasMany('EdestekKurulum');
        }
}
