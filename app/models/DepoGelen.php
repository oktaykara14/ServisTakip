<?php

class DepoGelen extends Eloquent {

	protected $table = 'depogelen';
    public $timestamps = false;
    protected $fillable = ['periyodik','kayitdurum','adet'];

    public function hatirlatma()
    {
        return $this->hasMany('Hatirlatma');
    }

    public function netsiscari()
    {
        return $this->belongsTo('NetsisCari');
    }

    public function periyodik()
    {
        return $this->hasMany('Periyodik');
    }
}
