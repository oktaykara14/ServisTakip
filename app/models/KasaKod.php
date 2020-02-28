<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class KasaKod extends Eloquent {

    use SoftDeletingTrait;
    protected $table = 'kasakod';
    public $timestamps = false;
    protected $dates = ['deleted_at'];

}
