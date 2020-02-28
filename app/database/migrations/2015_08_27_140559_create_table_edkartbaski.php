<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableEdkartbaski extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('edestekkartbaski', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('edestekbaskitur_id')->unsigned();
            $table->foreign('edestekbaskitur_id')->references('id')->on('edestekbaskitur');
			$table->string('onresim')->nullable();
			$table->string('arkaresim')->nullable();
                        
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('edestekkartbaski');
	}

}
