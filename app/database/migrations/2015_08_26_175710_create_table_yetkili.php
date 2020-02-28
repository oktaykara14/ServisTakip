<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableYetkili extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('yetkili', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('adisoyadi',512);
			$table->string('email')->nullable();
            $table->string('telefon')->nullable();
			$table->integer('netsiscari_id')->unsigned();
            $table->foreign('netsiscari_id')->references('id')->on('netsiscari');
			$table->integer('uretimyer_id')->unsigned();
            $table->foreign('uretimyer_id')->references('id')->on('uretimyer');
            $table->integer('servis_id')->unsigned();
            $table->foreign('servis_id')->references('id')->on('servis');
            $table->integer('aktif')->default(1);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('yetkili');
	}

}
