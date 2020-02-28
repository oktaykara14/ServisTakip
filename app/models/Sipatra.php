<?php

class Sipatra extends Eloquent {

	protected $table = 'TBLSIPATRA';
    public $timestamps = false;
    protected $connection = 'sqlsrv2';
    protected  $primaryKey = 'INCKEYNO';

}
