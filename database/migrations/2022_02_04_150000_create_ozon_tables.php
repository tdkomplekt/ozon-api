<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOzonTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->down();

        Schema::create('ozon_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->default(0)->index();
            $table->string('name')->nullable()->default(null)->index();
            $table->string('full_name')->nullable()->default(null)->index();
            $table->boolean('last_node')->default(0)->index();
        });

        Schema::create('ozon_attributes', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable()->default(null);
            $table->text('description')->nullable()->default(null);
            $table->string('type')->nullable()->default(null);

            $table->boolean('is_collection')->default(false);
            $table->foreignId('dictionary_id')->default(0);
        });

        Schema::create('ozon_category_attribute', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ozon_category_id')->index();
            $table->foreignId('ozon_attribute_id')->index();

            $table->boolean('is_required')->default(false);
            $table->foreignId('group_id')->default(0);
            $table->string('group_name')->default('');


            $table->foreign('ozon_category_id')->references('id')->on('ozon_categories')->onDelete('cascade');
            $table->foreign('ozon_attribute_id')->references('id')->on('ozon_attributes')->onDelete('cascade');
        });

        Schema::create('ozon_attribute_options', function (Blueprint $table) {
            $table->id();
            $table->string('value');
            $table->string('info');
            $table->string('picture');
        });

        Schema::create('ozon_category_attribute_option', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ozon_category_id')->index();
            $table->foreignId('ozon_attribute_id')->index();
            $table->foreignId('ozon_attribute_option_id')->index();

            $table->foreign('ozon_attribute_id')->references('id')->on('ozon_attributes')->onDelete('cascade');
            $table->foreign('ozon_attribute_option_id')->references('id')->on('ozon_attribute_options')->onDelete('cascade');
        });

        Schema::create('ozon_products', function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->nullable()->default(null);

            $table->string('offer_id')->index();
            $table->foreignId('category_id')->index();
            $table->string('name');

            $table->string('barcode')->nullable()->default(null);

            $table->float('price')->nullable()->default(null);
            $table->float('old_price')->nullable()->default(null);
            $table->float('premium_price')->nullable()->default(null);
            $table->float('vat', 2)->nullable()->default(null);;

            $table->integer('weight')->nullable()->default(null);
            $table->enum('weight_unit', ['g'])->default('g');

            $table->integer('width')->nullable()->default(null);
            $table->integer('height')->nullable()->default(null);
            $table->integer('depth')->nullable()->default(null);
            $table->enum('dimension_unit', ['mm'])->default('mm');

            $table->json('attributes')->nullable()->default(null);
            $table->json('complex_attributes')->nullable()->default(null);

            $table->string('primary_image')->nullable()->default(null);
            $table->string('color_image')->nullable()->default(null);

            $table->json('images')->nullable()->default(null);
            $table->json('images360')->nullable()->default(null);
            $table->json('pdf_list')->nullable()->default(null);

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('ozon_tasks', function (Blueprint $table) {
            $table->id();
            $table->json('response')->nullable()->default(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ozon_tasks');
        Schema::dropIfExists('ozon_category_attribute_option');
        Schema::dropIfExists('ozon_attribute_options');
        Schema::dropIfExists('ozon_category_attribute');
        Schema::dropIfExists('ozon_products');
        Schema::dropIfExists('ozon_attributes');
        Schema::dropIfExists('ozon_categories');
    }
}
