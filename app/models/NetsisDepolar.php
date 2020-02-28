<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class NetsisDepolar extends Eloquent {

    use SoftDeletingTrait;
	protected $table = 'netsisdepolar';
	public $timestamps = false;
    protected $dates = ['deleted_at'];

    public function sube()
    {
        return $this->hasMany('Sube');
    }

}
