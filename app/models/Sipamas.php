<?php

use Illuminate\Database\Eloquent\Builder;

class Sipamas extends Eloquent {

	protected $table = 'TBLSIPAMAS';
    public $timestamps = false;
    protected $connection = 'sqlsrv2';
    protected $primaryKey = ['SUBE_KODU','FTIRSIP','FATIRS_NO','CARI_KODU'];
    public $incrementing = false;

    protected function getKeyForSaveQuery()
    {

        $primaryKeyForSaveQuery = array(count($this->primaryKey));

        foreach ($this->primaryKey as $i => $pKey) {
            $primaryKeyForSaveQuery[$i] = isset($this->original[$this->getKeyName()[$i]])
                ? $this->original[$this->getKeyName()[$i]]
                : $this->getAttribute($this->getKeyName()[$i]);
        }

        return $primaryKeyForSaveQuery;

    }

    protected function setKeysForSaveQuery(Builder $query)
    {

        foreach ($this->primaryKey as $i => $pKey) {
            $query->where($this->getKeyName()[$i], '=', $this->getKeyForSaveQuery()[$i]);
        }

        return $query;
    }
}
