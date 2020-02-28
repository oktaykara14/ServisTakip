<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableAbone extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('abone', function(Blueprint $table)
		{
			$table->increments('id');
            $table->string('adisoyadi',200);
            $table->string('abone_no',10)->nullable();
            $table->string('telefon',30)->nullable();
            $table->string('adresi',512)->nullable();
            $table->integer('uretimyer_id')->unsigned();
            $table->foreign('uretimyer_id')->references('id')->on('uretimyer');
            $table->integer('kullanici_id')->unsigned()->nullable();
            $table->foreign('kullanici_id')->references('id')->on('kullanici');
			$table->timestamps();
            $table->softDeletes();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('abone');
	}

}
