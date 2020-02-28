<?php

class HatirlatmaTip extends Eloquent {

	protected $table = 'hatirlatmatip';
    public $timestamps = false;

    public function hatirlatma()
    {
        return $this->hasMany('Hatirlatma');
    }

}
