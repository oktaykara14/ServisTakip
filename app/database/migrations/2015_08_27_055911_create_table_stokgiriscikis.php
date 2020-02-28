<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableStokgiriscikis extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('stokgiriscikis', function(Blueprint $table)
		{
			$table->increments('id');
            $table->integer('degisenler_id')->unsigned();
            $table->foreign('degisenler_id')->references('id')->on('degisenler');
            $table->integer('netsisstokkod_id')->unsigned();
            $table->foreign('netsisstokkod_id')->references('id')->on('netsisstokkod');
            $table->string('stokkodu',10);
            $table->integer('miktar');
            $table->string('gckod',2);
            $table->string('aciklama');
            $table->timestamp('tarih');
            $table->integer('servis_id')->unsigned();
            $table->foreign('servis_id')->references('id')->on('servis');
            $table->integer('kullanici_id')->unsigned();
            $table->foreign('kullanici_id')->references('id')->on('kullanici');
            $table->boolean('durum');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('stokgiriscikis');
	}

}
