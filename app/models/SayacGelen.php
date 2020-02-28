<?php

class SayacGelen extends Eloquent {

	protected $table = 'sayacgelen';
    const CREATED_AT = 'eklenmetarihi';
    const UPDATED_AT = 'guncellenmetarihi';

    public function sayacadi()
    {
        return $this->belongsTo('SayacAdi');
    }
}
