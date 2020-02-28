<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;
class SayacMarka extends Eloquent {

    use SoftDeletingTrait;
	protected $table = 'sayacmarka';
        public $timestamps = false;
    protected $dates = ['deleted_at'];
        public function sayactip()
        {
            return $this->hasMany('SayacTip');
        }
        
        public function sayacadi()
        {
            return $this->hasMany('SayacAdi');
        }
}
