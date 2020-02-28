<?php

class NetsisSube extends Eloquent {

	protected $table = 'TBLSUBELER';
    public $timestamps = false;
    protected $connection = 'sqlsrv2';
    protected  $primaryKey = 'SUBEKODU';

}
