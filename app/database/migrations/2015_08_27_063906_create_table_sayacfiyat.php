<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableSayacfiyat extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sayacfiyat', function(Blueprint $table)
		{
			$table->increments('id');
            $table->integer('uretimyer_id')->unsigned();
            $table->foreign('uretimyer_id')->references('id')->on('uretimyer');
            $table->integer('sayacadi_id')->unsigned();
            $table->foreign('sayacadi_id')->references('id')->on('sayacadi');
            $table->integer('sayactur_id')->unsigned();
            $table->foreign('sayactur_id')->references('id')->on('sayactur');
            $table->integer('sayaccap_id')->unsigned()->default(1);
            $table->foreign('sayaccap_id')->references('id')->on('sayaccap');
            $table->decimal('fiyat',10,2);
            $table->integer('parabirimi_id')->unsigned();
            $table->foreign('parabirimi_id')->references('id')->on('parabirimi');
            $table->boolean('durum')->default(true);
            $table->timestamps();
            $table->integer('kullanici_id')->unsigned();
            $table->foreign('kullanici_id')->references('id')->on('kullanici');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('sayacfiyat');
	}

}
