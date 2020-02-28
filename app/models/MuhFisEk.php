<?php

class MuhFisEk extends Eloquent {

	protected $table = 'TBLMUHFISEK';
    public $timestamps = false;
    protected $connection = 'sqlsrv2';
    protected  $primaryKey = 'INCKEY_NO';
    public $incrementing = false;
}
