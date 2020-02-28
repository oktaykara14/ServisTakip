<?php

class BasimLog extends Eloquent {

	protected $table = 'TBLDZNBASIMLOG';
    public $timestamps = false;
    protected $connection = 'sqlsrv2';
    protected  $primaryKey = 'KAYITNO';

}
