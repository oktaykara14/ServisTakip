<?php

class EfaturaParam extends Eloquent {

	protected $table = 'FATURAPARAM';
    public $timestamps = false;
    protected $connection = 'sqlsrv4';
    protected  $primaryKey = 'ID';

}
