<?php

class Proje extends Eloquent {

	protected $table = 'TBLPROJE';
    public $timestamps = false;
    protected $connection = 'sqlsrv2';
    protected  $primaryKey = 'PROJE_KODU';

}
