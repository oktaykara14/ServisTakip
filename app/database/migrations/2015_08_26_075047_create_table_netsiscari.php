<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableNetsiscari extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('netsiscari', function(Blueprint $table)
		{
			$table->increments('id');
            $table->string('carikod')->unique();
            $table->string('cariadi')->nullable();
            $table->string('tel')->nullable();
            $table->string('email')->nullable();
            $table->string('adres')->nullable();
            $table->string('il')->nullable();
            $table->string('ilce')->nullable();
            $table->integer('vadegunu')->default(0)->nullable();
            $table->string('caridurum',1)->nullable()->default('A');
            $table->softDeletes();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('netsiscari');
	}

}
