<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('Zakaznik', function (Blueprint $table) {
            $table->increments('id');
            $table->string('meno', 100);
            $table->string('email', 150)->unique();
            $table->string('telefon', 20)->nullable();
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('Doprava', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nazov', 100)->nullable();
            $table->decimal('cena', 10, 2)->nullable()->comment('check >= 0');
            $table->integer('odhad_dni')->nullable();
            $table->boolean('aktivna')->nullable();
        });

        Schema::create('Platba', function (Blueprint $table) {
            $table->increments('id');
            $table->string('sposob_platby', 100)->nullable()->comment('do buducna najskor enum');
            $table->decimal('poplatok', 10, 2)->nullable()->comment('check >= 0');
            $table->boolean('aktivna')->nullable();
        });

        Schema::create('Kategoria', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nazov', 100)->nullable();
            $table->unsignedInteger('parentId')->nullable();
            $table->foreign('parentId')->references('id')->on('Kategoria');
        });

        Schema::create('Produkt', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nazov', 150)->nullable();
            $table->text('popis')->nullable();
            $table->decimal('zakladna_cena', 10, 2)->nullable()->comment('check >= 0');
            $table->unsignedInteger('kategoriaId')->nullable();
            $table->boolean('aktivny')->nullable();
            $table->timestamp('created_at')->nullable();

            $table->foreign('kategoriaId')->references('id')->on('Kategoria');
        });

        Schema::create('Adresa', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('zakaznikId');
            $table->string('meno', 100);
            $table->string('ulica', 150);
            $table->string('mesto', 100);
            $table->string('psc', 20);
            $table->string('stat', 100);
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('zakaznikId')->references('id')->on('Zakaznik');
        });

        Schema::create('VariantProduktu', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('produktId');
            $table->string('nazov', 100);
            $table->decimal('cena', 10, 2)->comment('check >= 0');
            $table->integer('skladom')->comment('check >= 0');
            $table->boolean('aktivny')->nullable();

            $table->foreign('produktId')->references('id')->on('Produkt');
        });

        Schema::create('ProduktovyObrazok', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('produktId')->nullable();
            $table->text('url')->nullable();
            $table->integer('poradie')->nullable();

            $table->foreign('produktId')->references('id')->on('Produkt');
        });

        Schema::create('Objednavka', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('zakaznikId');
            $table->unsignedInteger('adresaId');
            $table->unsignedInteger('dopravaId');
            $table->unsignedInteger('platbaId');
            $table->enum('stav', ['PENDING', 'PAID', 'SHIPPED', 'DELIVERED', 'CANCELLED'])->default('PENDING');
            $table->decimal('subtotal', 10, 2)->nullable()->comment('check >= 0');
            $table->decimal('doprava_cena', 10, 2)->nullable()->comment('check >= 0');
            $table->decimal('platba_poplatok', 10, 2)->nullable()->comment('check >= 0');
            $table->decimal('total', 10, 2)->nullable()->comment('check >= 0');
            $table->timestamp('created_at')->nullable();

            $table->foreign('zakaznikId')->references('id')->on('Zakaznik');
            $table->foreign('adresaId')->references('id')->on('Adresa');
            $table->foreign('dopravaId')->references('id')->on('Doprava');
            $table->foreign('platbaId')->references('id')->on('Platba');
        });

        Schema::create('PolozkaObjednavky', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('objednavkaId');
            $table->unsignedInteger('variantId');
            $table->integer('mnozstvo')->nullable();
            $table->decimal('jednotkova_cena', 10, 2)->nullable()->comment('check >= 0');
            $table->decimal('celkova_cena', 10, 2)->nullable()->comment('check >= 0');

            $table->foreign('objednavkaId')->references('id')->on('Objednavka');
            $table->foreign('variantId')->references('id')->on('VariantProduktu');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
      Schema::dropIfExists('PolozkaObjednavky');
      Schema::dropIfExists('Objednavka');
      Schema::dropIfExists('ProduktovyObrazok');
      Schema::dropIfExists('VariantProduktu');
      Schema::dropIfExists('Adresa');
      Schema::dropIfExists('Produkt');
      Schema::dropIfExists('Kategoria');
      Schema::dropIfExists('Platba');
      Schema::dropIfExists('Doprava');
      Schema::dropIfExists('Zakaznik');
    }
};
