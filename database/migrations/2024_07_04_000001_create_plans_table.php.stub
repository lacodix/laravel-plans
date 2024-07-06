<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Lacodix\LaravelPlans\Enums\Interval;

return new class() extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create(config('plans.tables.plans'), static function (Blueprint $table): void {
            $table->id();
            $table->string('slug')->unique();
            $table->json('name');
            $table->json('description')->nullable();
            $table->decimal('price')->default('0.00');
            $table->boolean('active')->default(true);

            $table->decimal('signup_fee')->default('0.00');
            $table->unsignedSmallInteger('trial_period')->default(0);
            $table->string('trial_interval')->default(Interval::DAY);
            $table->unsignedSmallInteger('billing_period')->default(1);
            $table->string('billing_interval')->default(Interval::MONTH);
            $table->unsignedSmallInteger('grace_period')->default(0);
            $table->string('grace_interval')->default(Interval::DAY);

            $table->unsignedSmallInteger('order')->default(0);

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
        Schema::dropIfExists(config('plans.tables.plans'));
    }
};
