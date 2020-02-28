<?php

class SayacAdi extends Eloquent {

	protected $table = 'sayacadi';
        public $timestamps = false;
        
        public function sayacmarka()
        {
            return $this->belongsTo('SayacMarka');
        }
        
        public function sayactip()
        {
            return $this->belongsTo('SayacTip');
        }
        
        public function sayacfiyat()
        {
            return $this->hasMany('SayacFiyat');
        }
        
        public function sayacparca()
        {
            return $this->hasMany('SayacParca');
        }
        
        public function sayacgaranti()
        {
            return $this->hasMany('SayacGaranti');
        }
    public function arizafiyat()
    {
        return $this->hasMany('ArizaFiyat');
    }

    public function sayacgelen()
    {
        return $this->hasMany('SayacGelen');
    }
}
