<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;
class EdUrun extends Eloquent {

    use SoftDeletingTrait;
	protected $table = 'edurun';
    protected $guarded = [];

    public function kategoriler()
    {
        return $this->belongsToMany('EdKategori','edkategori_urun');
    }

    public function parcalar()
    {
        return $this->belongsToMany('EdParca','edurun_parca','urun_id','parca_id');
    }

}
