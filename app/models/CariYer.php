<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class CariYer extends Eloquent {
    use SoftDeletingTrait;
	protected $table = 'cariyer';
    protected $dates = ['deleted_at'];
  //public $timestamps = false;

    public function netsiscari()
    {
        return $this->belongsTo('NetsisCari');
    }
    public function uretimyer()
    {
        return $this->belongsTo('UretimYer');
    }
    public function kullanici()
    {
        return $this->belongsTo('Kullanici');
    }
}
