<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableEdmusteri extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('edestekmusteri', function(Blueprint $table)
		{
			$table->increments('id');
                        $table->string('musteriadi');
                        $table->timestamp('baslangictarihi')->nullable();
                        $table->timestamp('bitistarihi')->nullable();
			$table->string('projeresim')->nullable();
			$table->string('projedetay')->nullable();
			$table->integer('edestekmusteribilgi_id')->unsigned()->nullable();
                        $table->foreign('edestekmusteribilgi_id')->references('id')->on('edestekmusteribilgi');
			$table->integer('edesteksistembilgi_id')->unsigned()->nullable();
                        $table->foreign('edesteksistembilgi_id')->references('id')->on('edesteksistembilgi');
			$table->string('edestekbaskiidler')->default('');
			$table->string('urunturleri')->default('');
			$table->string('programturleri')->default('');
			$table->string('baskiturleri')->default('');
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
		Schema::drop('edestekmusteri');
	}

}
