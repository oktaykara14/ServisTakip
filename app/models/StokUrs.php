<?php

class StokUrs extends Eloquent {

	protected $table = 'TBLSTOKURS';
    public $timestamps = false;
    protected $connection = 'sqlsrv2';
    protected  $primaryKey = 'INCKEYNO';

}
