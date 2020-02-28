<?php

class Kasa extends Eloquent {

	protected $table = 'TBLKASA';
    public $timestamps = false;
    protected $connection = 'sqlsrv2';
    protected $primaryKey = 'SIRA';

}
