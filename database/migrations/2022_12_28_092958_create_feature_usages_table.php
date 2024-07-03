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
        Schema::create(config('plans.tables.feature_usages'), static function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(config('plans.models.subscription'))->constrained();
            $table->foreignIdFor(config('plans.models.feature'))->constrained();
            $table->integer('used')->default(0);
            $table->dateTime('valid_until')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists(config('plans.tables.feature_usages'));
    }
};
