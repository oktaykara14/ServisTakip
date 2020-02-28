<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class UretimSonu extends Eloquent {
    use SoftDeletingTrait;

	protected $table = 'uretimsonu';
    public $timestamps = false;
    protected $dates = ['deleted_at'];
}
