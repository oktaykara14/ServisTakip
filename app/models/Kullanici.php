<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;
use Illuminate\Database\Eloquent\SoftDeletingTrait;
use Traits\Messagable;

class Kullanici extends Eloquent implements UserInterface, RemindableInterface{

	use UserTrait, RemindableTrait,SoftDeletingTrait,Messagable;
//        public $timestamps = false;
	protected $table = 'kullanici';
        public static $rules = ['girisadi'=>'required','sifre'=>'required'];
	protected $hidden = array('password', 'remember_token');
    protected $fillable = ['last_active','aktifdurum'];
    protected $dates = ['deleted_at'];
    public function grup()
    {
        return $this->belongsTo('Grup');
    }

    public function servis()
    {
        return $this->belongsTo('Servis');
    }

    public function sayacfiyat()
    {
        return $this->hasMany('SayacFiyat');
    }

    public function edestekpersonel()
    {
        return $this->hasMany('EdestekPersonel');
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
    public function mesaj()
    {
        return $this->hasMany('Mesaj');
    }
    public function ileti()
    {
        return $this->hasMany('Ileti');
    }
    public function gonderilen()
    {
        return $this->hasMany('Gonderilen');
    }

    public function servisyetkili()
    {
        return $this->hasMany('ServisYetkili');
    }
    public function alici()
    {
        return $this->hasMany('Alici');
    }

    public function yetkili()
    {
        return $this->hasMany('Yetkili');
    }
    public function beyanname()
    {
        return $this->hasMany('Beyanname');
    }

    public function subepersonel()
    {
        return $this->hasMany('SubePersonel');
    }

}
