<?php

class Sehirsabit extends Eloquent {

	protected $table = 'SEHIRSABIT';
    public $timestamps = false;
    protected $connection = 'sqlsrv3';
    protected  $primaryKey = 'SEHIRKODU';
}
