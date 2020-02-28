<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableEdpersonel extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('edestekpersonel', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('kullanici_id')->unsigned();
            $table->foreign('kullanici_id')->references('id')->on('kullanici');
            $table->string('adisoyadi');
            $table->string('meslek')->nullable();
            $table->string('mail')->nullable();
            $table->timestamp('giristarihi')->nullable();
            $table->string('ilgilendikleri')->nullable();
            $table->integer('sonislem_id')->nullable();
            $table->timestamp('sonislemtarihi')->nullable();
            $table->boolean('durum')->default(true);
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
		Schema::drop('edestekpersonel');
	}

}
