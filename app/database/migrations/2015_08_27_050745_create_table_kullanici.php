<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableKullanici extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('kullanici', function(Blueprint $table)
		{
			$table->increments('id');
            $table->string('adi');
            $table->string('soyadi');
            $table->string('adi_soyadi',512);
            $table->string('girisadi')->unique();
            $table->string('password');
            $table->string('mail')->unique()->nullable();
            $table->string('mailsifre')->nullable();
            $table->string('avatar')->default('');
            $table->integer('servis_id')->unsigned();
            $table->foreign('servis_id')->references('id')->on('servis');
            $table->integer('grup_id')->unsigned()->default(11);
            $table->foreign('grup_id')->references('id')->on('grup');
            $table->boolean('aktifdurum')->default(false);
            $table->timestamps();
            $table->softDeletes();
            $table->rememberToken();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('kullanici');
	}

}
