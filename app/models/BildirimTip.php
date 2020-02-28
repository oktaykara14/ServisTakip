<?php

class BildirimTip extends Eloquent {

	protected $table = 'bildirimtip';
    public $timestamps = false;

    public function hatirlatma()
    {
        return $this->hasMany('Hatirlatma');
    }
}
