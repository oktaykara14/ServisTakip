<?php

class EdEntegrasyonProgramSeeder extends Seeder {

	public function run()
	{
            EdestekEntegrasyonProgram::create([
                'program'=>'Epic'
            ]);
            EdestekEntegrasyonProgram::create([
                'program'=>'EpicSmart'
            ]);
            EdestekEntegrasyonProgram::create([
                'program'=>'3.Parti'
            ]);
	}

}
