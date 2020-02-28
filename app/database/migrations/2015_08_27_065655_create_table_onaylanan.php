<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableOnaylanan extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('onaylanan', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('servis_id')->unsigned();
            $table->foreign('servis_id')->references('id')->on('servis');
            $table->integer('uretimyer_id')->unsigned();
            $table->foreign('uretimyer_id')->references('id')->on('uretimyer');
            $table->integer('netsiscari_id')->unsigned();
            $table->foreign('netsiscari_id')->references('id')->on('netsiscari');
            $table->integer('ucretlendirilen_id')->unsigned();
            $table->foreign('ucretlendirilen_id')->references('id')->on('ucretlendirilen');
            $table->integer('yetkili_id')->unsigned();
            $table->foreign('yetkili_id')->references('id')->on('yetkili');
            $table->timestamp('onaytarihi');
            $table->integer('onaylamatipi');
            $table->string('onayformu',255)->nullable();
            $table->string('userip',100)->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('onaylanan');
	}

}
