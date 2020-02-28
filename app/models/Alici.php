<?php

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletingTrait;

class Alici extends Eloquent
{
    use SoftDeletingTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'alici';

    /**
     * The attributes that can be set with Mass Assignment.
     *
     * @var array
     */
    protected $fillable = ['ileti_id', 'kullanici_id', 'last_read'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'deleted_at', 'son_okuma'];

    /**
     * Thread relationship
     *
     * @return BelongsTo
     */
    public function ileti()
    {
        return $this->belongsTo('Ileti');
    }

    /**
     * User relationship
     *
     * @return BelongsTo
     */
    public function kullanici()
    {
        return $this->belongsTo('Kullanici');
    }
}
