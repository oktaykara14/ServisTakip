<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableKalibrasyonstandart extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('kalibrasyonstandart', function(Blueprint $table)
		{
			$table->increments('id');
            $table->integer('kalibrasyontipi_id')->unsigned();
            $table->foreign('kalibrasyontipi_id')->references('id')->on('kalibrasyontipi');
            $table->string('hassasiyet',100);
            $table->integer('noktasayisi');
            $table->string('nokta1',100);
            $table->decimal('sapma1',3,2);
            $table->string('nokta2',100);
            $table->decimal('sapma2',3,2);
            $table->string('nokta3',100);
            $table->decimal('sapma3',3,2);
            $table->string('nokta4',100)->nullable();
            $table->decimal('sapma4',3,2)->nullable();
            $table->string('nokta5',100)->nullable();
            $table->decimal('sapma5',3,2)->nullable();
            $table->string('nokta6',100)->nullable();
            $table->decimal('sapma6',3,2)->nullable();
            $table->string('nokta7',100)->nullable();
            $table->decimal('sapma7',3,2)->nullable();

		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('kalibrasyonstandart');
	}

}
