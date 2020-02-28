<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableHatirlatma extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('hatirlatma', function(Blueprint $table)
		{
			$table->increments('id');
            $table->integer('hatirlatmatip_id')->unsigned();
            $table->foreign('hatirlatmatip_id')->references('id')->on('hatirlatmatip');
            $table->integer('netsiscari_id')->unsigned();
            $table->foreign('netsiscari_id')->references('id')->on('netsiscari');
            $table->string('servisstokkodu',10)->nullable();
            $table->integer('depogelen_id')->unsigned()->nullable();
            $table->foreign('depogelen_id')->references('id')->on('depogelen');
            $table->integer('uretimyer_id')->unsigned()->nullable();
            $table->foreign('uretimyer_id')->references('id')->on('uretimyer');
            $table->integer('servis_id')->unsigned();
            $table->foreign('servis_id')->references('id')->on('servis');
            $table->timestamp('tarih');
            $table->boolean('durum');
            $table->integer('tip');
            $table->integer('adet');
            $table->integer('kalan');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('hatirlatma');
	}

}
