<?php

class Casabit extends Eloquent {

	protected $table = 'TBLCASABIT';
    public $timestamps = false;
    protected $connection = 'sqlsrv2';
    protected  $primaryKey = 'CARI_KOD';
    public $incrementing = false;

}
