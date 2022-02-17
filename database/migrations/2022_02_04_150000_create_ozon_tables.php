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
        Schema::create('ozon_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->default(0)->index();
            $table->string('title')->nullable()->default(null)->index();
            $table->string('search')->nullable()->default(null)->index();
            $table->boolean('last_node')->default(0)->index();
        });

        Schema::create('ozon_attributes', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable()->default(null);
            $table->text('description')->nullable()->default(null);
            $table->string('type')->nullable()->default(null);
            $table->boolean('is_collection')->default(false);
            $table->boolean('is_required')->default(false);
            $table->foreignId('group_id')->default(0);
            $table->string('group_name')->default('');
            $table->foreignId('dictionary_id')->default(0);
        });

        Schema::create('ozon_category_attribute', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ozon_category_id')->index();
            $table->foreignId('ozon_attribute_id')->index();

            $table->foreign('ozon_category_id')->references('id')->on('ozon_categories')->onDelete('cascade');
            $table->foreign('ozon_attribute_id')->references('id')->on('ozon_attributes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ozon_category_attribute');
        Schema::dropIfExists('ozon_categories');
        Schema::dropIfExists('ozon_attributes');
    }
}
