<?php

class Cahar extends Eloquent {

	protected $table = 'TBLCAHAR';
    public $timestamps = false;
    protected $connection = 'sqlsrv2';
    protected  $primaryKey = 'INC_KEY_NUMBER';

}
