<?php

class EarsivParam extends Eloquent {

	protected $table = 'ARSIVPARAM';
    public $timestamps = false;
    protected $connection = 'sqlsrv4';
    protected  $primaryKey = 'ID';

}
