<?php

class ParaBirimi extends Eloquent {

	protected $table = 'parabirimi';
        public $timestamps = false;
        
        public function uretimyer()
        {
            return $this->hasMany('UretimYer');
        }
        
        public function sayacfiyat()
        {
            return $this->hasMany('SayacFiyat');
        }

    public function fiyat()
    {
        return $this->hasMany('Fiyat');
    }

    public function ucretlendirilen()
    {
        return $this->hasMany('Ucretlendirilen');
    }
    public function arizafiyat()
    {
        return $this->hasMany('ArizaFiyat');
    }
}
