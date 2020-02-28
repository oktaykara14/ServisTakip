<?php

class Seritra extends Eloquent {

	protected $table = 'TBLSERITRA';
    public $timestamps = false;
    protected $connection = 'sqlsrv2';
    protected  $primaryKey = 'SIRA_NO';

}
