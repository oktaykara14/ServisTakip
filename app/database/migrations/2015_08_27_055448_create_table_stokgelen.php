<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableStokgelen extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('stokgelen', function(Blueprint $table)
		{
			$table->increments('id');
            $table->string('db_name',50);
            $table->integer('inckeyno');
			$table->string('stokkodu',10);
            $table->string('fisno',50);
            $table->integer('miktar');
            $table->string('gckod',2);
            $table->timestamp('tarih');
            $table->integer('alinandepo');
            $table->integer('alicidepo');
            $table->integer('plasiyerkodu');
            $table->boolean('durum')->default(false);
                        
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('stokgelen');
	}

}
