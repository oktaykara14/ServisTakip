<?php

class EfaturaKullanici extends Eloquent {

	protected $table = 'FATURAKULLLANICI';
    public $timestamps = false;
    protected $connection = 'sqlsrv4';
    protected $primaryKey = null;
    public $incrementing = false;

}
