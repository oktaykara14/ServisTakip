<?php

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mesaj extends Eloquent
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'mesaj';

    /**
     * The relationships that should be touched on save.
     *
     * @var array
     */
    protected $touches = ['ileti'];

    /**
     * The attributes that can be set with Mass Assignment.
     *
     * @var array
     */
    protected $fillable = ['ileti_id', 'kullanici_id', 'icerik'];

    /**
     * Validation rules.
     *
     * @var array
     */
    protected $rules = [
        'icerik' => 'required',
    ];

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

    /**
     * Participants relationship
     *
     * @return HasMany
     */
    public function alici()
    {
        return $this->hasMany('Alici', 'ileti_id', 'ileti_id');
    }

    /**
     * Recipients of this message
     *
     * @return HasMany
     */
    public function digeralicilar()
    {
        return $this->alici()->where('kullanici_id', '!=', $this->kullanici_id);
    }
}
