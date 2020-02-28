<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableStokdurum extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('stokdurum', function(Blueprint $table)
		{
			$table->increments('id');
            $table->integer('degisenler_id')->unsigned();
            $table->foreign('degisenler_id')->references('id')->on('degisenler');
            $table->integer('netsisstokkod_id')->unsigned();
            $table->foreign('netsisstokkod_id')->references('id')->on('netsisstokkod');
            $table->string('stokkodu');
            $table->integer('adet');
            $table->integer('servis_id')->unsigned();
            $table->foreign('servis_id')->references('id')->on('servis');
            $table->integer('kalan');
            $table->integer('kullanilan');
            $table->integer('biten');
                        
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('stokdurum');
	}

}
