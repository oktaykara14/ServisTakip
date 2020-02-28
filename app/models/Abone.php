<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Abone extends Eloquent {
    use SoftDeletingTrait;
	protected $table = 'abone';
    //public $timestamps = false;
    protected $dates = ['deleted_at'];

    public function getDateFormat()
    {
        return 'Y-m-d H:i:s.u';
    }

    public function fromDateTime($value)
    {
        return substr(parent::fromDateTime($value), 0, -3);
    }
}
