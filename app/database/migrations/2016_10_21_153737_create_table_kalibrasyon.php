<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableKalibrasyon extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('kalibrasyon', function(Blueprint $table)
		{
			$table->increments('id');
            $table->integer('sayacgelen_id')->unsigned();
            $table->foreign('sayacgelen_id')->references('id')->on('sayacgelen');
            $table->integer('sayacadi_id')->unsigned();
            $table->foreign('sayacadi_id')->references('id')->on('sayacadi');
            $table->string('kalibrasyon_seri',20);
            $table->date('imalyili')->nullable();
            $table->integer('istasyon_id')->unsigned()->nullable();
            $table->foreign('istasyon_id')->references('id')->on('istasyon');
            $table->integer('kalibrasyongrup_id')->unsigned();
            $table->foreign('kalibrasyongrup_id')->references('id')->on('kalibrasyongrup');
            $table->integer('kalibrasyonsayisi');
            $table->integer('sira')->nullable();
            $table->integer('kalibrasyonstandart_id')->unsigned()->nullable();
            $table->foreign('kalibrasyonstandart_id')->references('id')->on('kalibrasyonstandart');
            $table->integer('durum')->default(0);
            $table->integer('lf')->default(1);
            $table->integer('litre1')->nullable();
            $table->decimal('nokta1ilk',12,3)->nullable();
            $table->decimal('nokta1son',12,3)->nullable();
            $table->decimal('nokta1sapma',4,2)->nullable();
            $table->decimal('sonuc1',12,3)->nullable();
            $table->integer('litre2')->nullable();
            $table->decimal('nokta2ilk',12,3)->nullable();
            $table->decimal('nokta2son',12,3)->nullable();
            $table->decimal('nokta2sapma',4,2)->nullable();
            $table->decimal('sonuc2',12,3)->nullable();
            $table->integer('litre3')->nullable();
            $table->decimal('nokta3ilk',12,3)->nullable();
            $table->decimal('nokta3son',12,3)->nullable();
            $table->decimal('nokta3sapma',4,2)->nullable();
            $table->decimal('sonuc3',12,3)->nullable();
            $table->decimal('nokta4sapma',4,2)->nullable();
            $table->decimal('sonuc4',12,3)->nullable();
            $table->decimal('nokta5sapma',4,2)->nullable();
            $table->decimal('sonuc5',12,3)->nullable();
            $table->decimal('nokta6sapma',4,2)->nullable();
            $table->decimal('sonuc6',12,3)->nullable();
            $table->decimal('nokta7sapma',4,2)->nullable();
            $table->decimal('sonuc7',12,3)->nullable();
            $table->integer('hf2')->default(0);
            $table->decimal('hf2nokta1sapma',4,2)->nullable();
            $table->decimal('hf2sonuc1',12,3)->nullable();
            $table->decimal('hf2nokta2sapma',4,2)->nullable();
            $table->decimal('hf2sonuc2',12,3)->nullable();
            $table->decimal('hf2nokta3sapma',4,2)->nullable();
            $table->decimal('hf2sonuc3',12,3)->nullable();
            $table->decimal('hf2nokta4sapma',4,2)->nullable();
            $table->decimal('hf2sonuc4',12,3)->nullable();
            $table->decimal('hf2nokta5sapma',4,2)->nullable();
            $table->decimal('hf2sonuc5',12,3)->nullable();
            $table->decimal('hf2nokta6sapma',4,2)->nullable();
            $table->decimal('hf2sonuc6',12,3)->nullable();
            $table->decimal('hf2nokta7sapma',4,2)->nullable();
            $table->decimal('hf2sonuc7',12,3)->nullable();
            $table->integer('hf3')->default(0);
            $table->decimal('hf3nokta1sapma',4,2)->nullable();
            $table->decimal('hf3sonuc1',12,3)->nullable();
            $table->decimal('hf3nokta2sapma',4,2)->nullable();
            $table->decimal('hf3sonuc2',12,3)->nullable();
            $table->decimal('hf3nokta3sapma',4,2)->nullable();
            $table->decimal('hf3sonuc3',12,3)->nullable();
            $table->decimal('hf3nokta4sapma',4,2)->nullable();
            $table->decimal('hf3sonuc4',12,3)->nullable();
            $table->decimal('hf3nokta5sapma',4,2)->nullable();
            $table->decimal('hf3sonuc5',12,3)->nullable();
            $table->decimal('hf3nokta6sapma',4,2)->nullable();
            $table->decimal('hf3sonuc6',12,3)->nullable();
            $table->decimal('hf3nokta7sapma',4,2)->nullable();
            $table->decimal('hf3sonuc7',12,3)->nullable();
            $table->integer('kullanici_id')->unsigned()->nullable();
            $table->foreign('kullanici_id')->references('id')->on('kullanici');
            $table->timestamp('kalibrasyontarih')->nullable();
            $table->decimal('sicaklik',3,1)->nullable();
            $table->integer('bagilnem')->nullable();
            $table->decimal('hissedilen',3,1)->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('kalibrasyon');
	}

}
