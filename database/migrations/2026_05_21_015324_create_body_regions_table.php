<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('body_regions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_region_id')->nullable()->constrained('body_regions')->onDelete('cascade');
            $table->string('name');
            $table->string('name_medical')->nullable();
            $table->text('description')->nullable();
            $table->string('threejs_mesh_id')->nullable();
            $table->json('bounding_coordinates')->nullable();
            $table->string('icd10_code_prefix', 10)->nullable();
            $table->boolean('is_critical_region')->default(false);
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('parent_region_id');
            $table->index('is_critical_region');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('body_regions');
    }
};
