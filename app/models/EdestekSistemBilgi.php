<?php

class EdestekSistemBilgi extends Eloquent {

	protected $table = 'edesteksistembilgi';
        public $timestamps = false;

        public function edestekmusteri()
        {
            return $this->hasMany('EdestekMusteri');
        }

        public function edestekplasiyer()
        {
            return $this->hasMany('Plasiyer');
        }
        
        public function tumurunler()
        {
            if($this->urunler!=""){
                $urunler = explode(',', $this->urunler);
                return EdestekSistemUrun::whereIn('id',$urunler)->get();
            }else{
                return "";
            }
        }
        
        public function digerurunler()
        {
            if($this->urunler!=""){
                $urunler = explode(',', $this->urunler);
                $uruntur = EdestekSistemUrun::whereIn('id',$urunler)->lists('edestekurun_id');
                return EdestekUrun::whereNotIn('id', $uruntur)->get();
            }else{
                return EdestekUrun::all();
            }
        }
                
        public function tumprogramlar()
        {
            if($this->programlar!=""){
                $programlar = explode(',', $this->programlar);
                $programsecilen = EdestekSistemProgram::whereIn('id',$programlar)->get();
                $veritabanlar = explode(',', $this->veritabanlari);
                $databasesecilen = EdestekSistemDatabase::whereIn('id',$veritabanlar)->get();
                foreach($programsecilen as $program)
                {
                    foreach($databasesecilen as $database)
                    {
                        if($program->edestekprogram_id==$database->edestekdatabase_id)
                        {
                            $program->database=$database;
                        }
                    }
                }
                return $programsecilen;
            }else{
                return "";
            }
        }
        
        public function digerprogramlar()
        {
            if($this->programlar!=""){
                $programlar = explode(',', $this->programlar);
                $programsecilen = EdestekSistemProgram::whereIn('id',$programlar)->lists('edestekprogram_id');
                $veritabanlar = explode(',', $this->veritabanlari);
                $databasesecilen = EdestekSistemDatabase::whereIn('id',$veritabanlar)->lists('edestekdatabase_id');
                $digerprogram = EdestekProgram::whereNotIn('id', $programsecilen)->get();
                $digerdatabase = EdestekDatabase::whereNotIn('id', $databasesecilen)->get();
                foreach($digerprogram as $program)
                {
                    foreach($digerdatabase as $database)
                    {
                        if($program->id==$database->id)
                        {
                            $program->database=$database;
                        }
                    }
                }
                return $digerprogram;
            }else{
                return EdestekProgram::all();
            }
        }
}
