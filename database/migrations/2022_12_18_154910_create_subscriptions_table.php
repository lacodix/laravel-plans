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
        Schema::create(config('plans.tables.subscriptions'), static function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(config('plans.models.plan'))->constrained();
            $table->morphs('subscriber');
            $table->string('slug'); // only unique per subscriber
            $table->dateTime('starts_at')->nullable();
            $table->dateTime('trial_ends_at')->nullable();
            $table->dateTime('ends_at')->nullable();
            $table->dateTime('canceled_for')->nullable();
            $table->dateTime('canceled_at')->nullable();

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
        Schema::dropIfExists(config('plans.tables.subscriptions'));
    }
};
