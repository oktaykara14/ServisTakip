<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableEdmusteribilgi extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('edestekmusteribilgi', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('adresi')->nullable();
            $table->integer('iller_id')->unsigned()->nullable();
            $table->foreign('iller_id')->references('id')->on('iller');
			$table->string('telefon')->nullable();
			$table->string('mail')->nullable();
			$table->string('yetkiliadi')->nullable();
			$table->string('yetkilitel')->nullable();
			$table->string('teamid')->nullable();
			$table->string('teampass')->nullable();
			$table->string('ammyyid')->nullable();
			$table->string('alpemixid')->nullable();
			$table->string('alpemixpass')->nullable();
			$table->string('uzakip')->nullable();
			$table->string('uzakkullanici')->nullable();
			$table->string('uzakpass')->nullable();
                        
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('edestekmusteribilgi');
	}

}
