<?php

class CasabitEk extends Eloquent {

	protected $table = 'TBLCASABITEK';
    public $timestamps = false;
    protected $connection = 'sqlsrv2';
    protected  $primaryKey = 'CARI_KOD';
    public $incrementing = false;

}
