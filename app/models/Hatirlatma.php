<?php

class Hatirlatma extends Eloquent {

	protected $table = 'hatirlatma';
    public $timestamps = false;

    public function servis()
    {
        return $this->belongsTo('Servis');
    }

    public function depogelen()
    {
        return $this->belongsTo('DepoGelen');
    }

    public function netsiscari()
    {
        return $this->belongsTo('NetsisCari')->withTrashed();
    }

    public function hatirlatmatip()
    {
        return $this->belongsTo('HatirlatmaTip');
    }

    public function bildirimtip()
    {
        return $this->belongsTo('BildirimTip');
    }

    public function servisstokkod($depogelen_id)
    {
        $depogelen = DepoGelen::find($depogelen_id);
        return ServisStokKod::where('stokkodu',$depogelen->servisstokkodu)->first();
    }
}
