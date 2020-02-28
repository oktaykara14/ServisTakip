<?php


class Servis extends Eloquent {

	protected $table = 'servis';
    public $timestamps = false;

    public function hatirlatma()
    {
        return $this->hasMany('Hatirlatma');
    }

    public function kullanici()
    {
        return $this->hasMany('Kullanici');
    }
    public function beyanname()
    {
        return $this->hasMany('Beyanname');
    }
    public function beyannameno()
    {
        return $this->hasMany('BeyannameNo');
    }

}
