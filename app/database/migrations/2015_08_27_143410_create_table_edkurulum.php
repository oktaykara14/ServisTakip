<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableEdkurulum extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('edestekkurulum', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('edestekmusteri_id')->unsigned()->nullable();
                        $table->foreign('edestekmusteri_id')->references('id')->on('edestekmusteri');
                        $table->timestamp('kurulumtarihi');
			$table->integer('edestekkurulumtur_id')->unsigned()->nullable();
                        $table->foreign('edestekkurulumtur_id')->references('id')->on('edestekkurulumtur');
                        $table->string('detay')->nullable();
                        $table->string('tutanak')->nullable();
			$table->integer('edestekpersonel_id')->unsigned()->nullable();
                        $table->foreign('edestekpersonel_id')->references('id')->on('edestekpersonel');
                        $table->integer('durum')->default(0);
                        
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('edestekkurulum');
	}

}
