<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableFiyat extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('fiyat', function(Blueprint $table)
		{
			$table->increments('id');
            $table->integer('sayactur_id')->unsigned();
            $table->foreign('sayactur_id')->references('id')->on('sayactur');
            $table->integer('uretimyer_id')->unsigned();
            $table->foreign('uretimyer_id')->references('id')->on('uretimyer');
            $table->integer('degisenler_id')->unsigned();
            $table->foreign('degisenler_id')->references('id')->on('degisenler');
            $table->decimal('fiyat',10,2);
            $table->integer('parabirimi_id')->unsigned();
            $table->foreign('parabirimi_id')->references('id')->on('parabirimi');
            $table->boolean('durum')->default(true);
                        
                        
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('fiyat');
	}

}
