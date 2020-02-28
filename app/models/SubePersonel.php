<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class SubePersonel extends Eloquent {
    use SoftDeletingTrait;
	protected $table = 'subepersonel';
    //public $timestamps = false;
    protected $dates = ['deleted_at'];
    public function kullanici()
    {
        return $this->belongsTo('Kullanici');
    }
}
