<?php

class Fatuno extends Eloquent {

	protected $table = 'TBLFATUNO';
    public $timestamps = false;
    protected $connection = 'sqlsrv2';
    protected $primaryKey = null;
    public $incrementing = false;

}
