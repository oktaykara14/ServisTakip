<?php

use Illuminate\Database\Eloquent\SoftDeletingTrait;

class AboneTahsis extends Eloquent
{

    use SoftDeletingTrait;
    public $timestamps = false;
    protected $table = 'abonetahsis';
    protected $dates = ['deleted_at'];
}
