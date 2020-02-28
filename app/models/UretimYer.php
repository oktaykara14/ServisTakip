<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;
class UretimYer extends Eloquent{

    use SoftDeletingTrait;
	protected $table = 'uretimyer';
    public $timestamps = false;
    protected $dates = ['deleted_at'];

    public function parabirimi()
    {
        return $this->belongsTo('ParaBirimi');
    }

    public function sayacfiyat()
    {
        return $this->hasMany('SayacFiyat');
    }

    public function sayacgaranti()
    {
        return $this->hasMany('SayacGaranti');
    }

    public function fiyat()
    {
        return $this->hasMany('Fiyat');
    }

    public function ucretlendirilen()
    {
        return $this->hasMany('Ucretlendirilen');
    }

    public function depolararasi()
    {
        return $this->hasMany('Depolararasi');
    }
    public function cariyer()
    {
        return $this->hasMany('CariYer');
    }

}
