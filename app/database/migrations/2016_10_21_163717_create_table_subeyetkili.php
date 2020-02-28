<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableSubeyetkili extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('subeyetkili', function(Blueprint $table)
		{
			$table->increments('id');
            $table->string('email')->nullable();
            $table->string('telefon')->nullable();
            $table->integer('netsiscari_id')->unsigned()->nullable();
            $table->foreign('netsiscari_id')->references('id')->on('netsiscari');
            $table->integer('uretimyer_id')->unsigned()->nullable();
            $table->foreign('uretimyer_id')->references('id')->on('uretimyer');
            $table->integer('servis_id')->unsigned()->nullable();
            $table->foreign('servis_id')->references('id')->on('servis');
            $table->integer('kullanici_id')->unsigned()->nullable();
            $table->foreign('kullanici_id')->references('id')->on('kullanici');
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
		Schema::drop('subeyetkili');
	}

}
