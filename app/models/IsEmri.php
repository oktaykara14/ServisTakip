<?php

class IsEmri extends Eloquent {

	protected $table = 'TBLISEMRI';
    public $timestamps = false;
    protected $connection = 'sqlsrv2';
    protected  $primaryKey = 'ISEMRINO';
    public $incrementing = false;
}
