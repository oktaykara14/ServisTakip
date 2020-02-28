<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableNetsisstokkod extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('netsisstokkod', function(Blueprint $table)
		{
			$table->increments('id');
            $table->string('kodu',10);
            $table->string('adi',100);
            $table->string('grupkodu',20);
            $table->string('kod1',20);
            $table->string('kod2',20);
            $table->string('kod3',20);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('netsisstokkod');
	}

}
