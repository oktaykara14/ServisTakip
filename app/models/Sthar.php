<?php

class Sthar extends Eloquent {

	protected $table = 'TBLSTHAR';
    public $timestamps = false;
    protected $connection = 'sqlsrv2';
    protected  $primaryKey = 'INCKEYNO';

}
