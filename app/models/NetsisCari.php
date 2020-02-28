<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class NetsisCari extends Eloquent {

    use SoftDeletingTrait;
	protected $table = 'netsiscari';
    public $timestamps = false;
    protected $dates = ['deleted_at'];

    public function hatirlatma()
    {
        return $this->hasMany('Hatirlatma');
    }
    public function cariyer()
    {
        return $this->hasMany('CariYer');
    }
    public function yetkili()
    {
        return $this->hasMany('Yetkili');
    }
    public function depogelen()
    {
        return $this->hasMany('DepoGelen');
    }
}
