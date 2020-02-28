<?php
use Illuminate\Database\Eloquent\SoftDeletingTrait;
class EdParca extends Eloquent {

    use SoftDeletingTrait;
	protected $table = 'edparca';
    protected $guarded = [];

    public function alt_parcalar()
    {
        return $this->hasMany('EdParca','ust_id','id');
    }

    public function urunler()
    {
        return $this->belongsToMany('EdUrun','edurun_parca');
    }


}
