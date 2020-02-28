<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Rapor extends Eloquent {
    use SoftDeletingTrait;
	protected $table = 'rapor';
    //public $timestamps = false;
    protected $dates = ['created_at','updated_at','deleted_at'];

}
