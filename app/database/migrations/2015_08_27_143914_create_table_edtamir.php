<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableEdtamir extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('edestektamir', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('edestekmusteri_id')->unsigned()->nullable();
                        $table->foreign('edestekmusteri_id')->references('id')->on('edestekmusteri');
			$table->integer('edestektamirurun_id')->unsigned()->nullable();
                        $table->foreign('edestektamirurun_id')->references('id')->on('edestektamirurun');
                        $table->string('urunadi')->nullable();
                        $table->timestamp('gelistarihi')->nullable();
			$table->integer('edestektamirislem_id')->unsigned()->nullable();
                        $table->foreign('edestektamirislem_id')->references('id')->on('edestektamirislem');
                        $table->string('detay')->nullable();
                        $table->timestamp('sevktarihi')->nullable();
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
		Schema::drop('edestektamir');
	}

}
