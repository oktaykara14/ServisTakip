<?php

class EfaturaCari extends Eloquent {

	protected $table = 'FATURACARIETIKET';
    public $timestamps = false;
    protected $connection = 'sqlsrv4';
    protected $primaryKey = null;
    public $incrementing = false;

}
