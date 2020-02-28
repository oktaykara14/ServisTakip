<?php

class EarsivFatura extends Eloquent {

	protected $table = 'ARSIVGONDERILMIS';
    public $timestamps = false;
    protected $connection = 'sqlsrv4';
    protected  $primaryKey = 'ID';

}
