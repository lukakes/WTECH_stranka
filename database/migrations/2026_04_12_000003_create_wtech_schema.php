<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('zakaznici', function (Blueprint $table) {
            $table->increments('id');
            $table->string('meno', 100);
            $table->string('email', 150)->unique();
            $table->string('telefon', 20)->nullable();
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('dopravy', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nazov', 100)->nullable();
            $table->decimal('cena', 10, 2)->nullable();
            $table->integer('odhad_dni')->nullable();
            $table->boolean('aktivna')->nullable();
        });

        Schema::create('platby', function (Blueprint $table) {
            $table->increments('id');
            $table->string('sposob_platby', 100)->nullable();
            $table->decimal('poplatok', 10, 2)->nullable();
            $table->boolean('aktivna')->nullable();
        });

        Schema::create('kategorie', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nazov', 100)->nullable();
            $table->unsignedInteger('parent_id')->nullable();

            $table->foreign('parent_id')->references('id')->on('kategorie');
        });

        Schema::create('produkty', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nazov', 150)->nullable();
            $table->text('popis')->nullable();
            $table->decimal('zakladna_cena', 10, 2)->nullable();
            $table->unsignedInteger('kategoria_id')->nullable();
            $table->boolean('aktivny')->nullable();
            $table->timestamp('created_at')->nullable();

            $table->foreign('kategoria_id')->references('id')->on('kategorie');
        });

        Schema::create('adresy', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('zakaznik_id');
            $table->string('meno', 100);
            $table->string('ulica', 150);
            $table->string('mesto', 100);
            $table->string('psc', 20);
            $table->string('stat', 100);
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('zakaznik_id')->references('id')->on('zakaznici');
        });

        Schema::create('varianty_produktu', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('produkt_id');
            $table->string('nazov', 100);
            $table->decimal('cena', 10, 2);
            $table->integer('skladom');
            $table->boolean('aktivny')->nullable();

            $table->foreign('produkt_id')->references('id')->on('produkty');
        });

        Schema::create('produktove_obrazky', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('produkt_id')->nullable();
            $table->text('url')->nullable();
            $table->integer('poradie')->nullable();

            $table->foreign('produkt_id')->references('id')->on('produkty');
        });

        Schema::create('objednavky', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('zakaznik_id');
            $table->unsignedInteger('adresa_id');
            $table->unsignedInteger('doprava_id');
            $table->unsignedInteger('platba_id');
            $table->enum('stav', ['PENDING', 'PAID', 'SHIPPED', 'DELIVERED', 'CANCELLED'])->default('PENDING');
            $table->decimal('subtotal', 10, 2)->nullable();
            $table->decimal('doprava_cena', 10, 2)->nullable();
            $table->decimal('platba_poplatok', 10, 2)->nullable();
            $table->decimal('total', 10, 2)->nullable();
            $table->timestamp('created_at')->nullable();

            $table->foreign('zakaznik_id')->references('id')->on('zakaznici');
            $table->foreign('adresa_id')->references('id')->on('adresy');
            $table->foreign('doprava_id')->references('id')->on('dopravy');
            $table->foreign('platba_id')->references('id')->on('platby');
        });

        Schema::create('polozky_objednavky', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('objednavka_id');
            $table->unsignedInteger('variant_id');
            $table->integer('mnozstvo')->nullable();
            $table->decimal('jednotkova_cena', 10, 2)->nullable();
            $table->decimal('celkova_cena', 10, 2)->nullable();

            $table->foreign('objednavka_id')->references('id')->on('objednavky');
            $table->foreign('variant_id')->references('id')->on('varianty_produktu');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('polozky_objednavky');
        Schema::dropIfExists('objednavky');
        Schema::dropIfExists('produktove_obrazky');
        Schema::dropIfExists('varianty_produktu');
        Schema::dropIfExists('adresy');
        Schema::dropIfExists('produkty');
        Schema::dropIfExists('kategorie');
        Schema::dropIfExists('platby');
        Schema::dropIfExists('dopravy');
        Schema::dropIfExists('zakaznici');
    }
};
