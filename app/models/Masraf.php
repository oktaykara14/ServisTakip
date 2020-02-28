<?php

class Masraf extends Eloquent {

	protected $table = 'TBLMASRAF';
    public $timestamps = false;
    protected $connection = 'sqlsrv2';
    protected $primaryKey = 'MKOD';
}
