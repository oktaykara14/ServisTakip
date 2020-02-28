<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;
class EdKategori extends Eloquent {

    use SoftDeletingTrait;
	protected $table = 'edkategori';
    protected $guarded = [];

    public function alt_kategoriler()
    {
        return $this->hasMany('EdKategori','ust_id','id');
    }

    public function urunler()
    {
        return $this->belongsToMany('EdUrun','edkategori_urun','kategori_id','urun_id');
    }

}
