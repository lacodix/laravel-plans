<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create(config('plans.tables.feature_plan'), static function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(config('plans.models.plan'))->constrained();

            $table->foreignIdFor(config('plans.models.feature'))->constrained();

            $table->unsignedSmallInteger('order')->default(0);
            $table->integer('value')->nullable();
            $table->unsignedSmallInteger('resettable_period')->nullable();
            $table->string('resettable_interval')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists(config('plans.tables.feature_plan'));
    }
};
