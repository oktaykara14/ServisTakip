<?php

class SayacCap extends Eloquent {

	protected $table = 'sayaccap';
        public $timestamps = false;
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
}
