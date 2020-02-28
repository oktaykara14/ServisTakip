<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableNetsisdepolar extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('netsisdepolar', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('kodu');
            $table->string('adi');
            $table->integer('netsiscari_id')->unsigned()->nullable();
            $table->foreign('netsiscari_id')->references('id')->on('netsiscari');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('netsisdepolar');
	}

}
