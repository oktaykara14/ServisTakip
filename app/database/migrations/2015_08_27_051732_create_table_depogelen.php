<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableDepogelen extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('depogelen', function(Blueprint $table)
		{
			$table->increments('id');
            $table->string('db_name',50);
            $table->integer('inckeyno');
			$table->string('fisno',20);
            $table->string('servisstokkodu',10);
            $table->timestamp('tarih');
            $table->integer('adet');
            $table->string('kullanici',20)->nullable();
            $table->integer('servis_id')->unsigned()->default(0);
            $table->foreign('servis_id')->references('id')->on('servis');
            $table->string('serviskodu',20);
            $table->string('carikod',50);
            $table->integer('depokodu');
            $table->integer('durum')->default(0);
            $table->integer('kayitdurum')->default(0);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('depogelen');
	}

}
