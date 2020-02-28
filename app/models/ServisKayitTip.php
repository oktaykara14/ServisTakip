<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class ServisKayitTip extends Eloquent {
    use SoftDeletingTrait;
	protected $table = 'serviskayittip';
    protected $dates = ['deleted_at'];

}
