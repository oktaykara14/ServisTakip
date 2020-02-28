<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class ServisKayit extends Eloquent {
    use SoftDeletingTrait;
	protected $table = 'serviskayit';
    protected $dates = ['deleted_at'];

}
