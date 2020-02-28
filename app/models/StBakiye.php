<?php

class StBakiye extends Eloquent {

	protected $table = 'MANAS_STBAKIYE';
    public $timestamps = false;
    protected $connection = 'sqlsrv2';
    protected $fillable = ['BAKIYE'];
}
