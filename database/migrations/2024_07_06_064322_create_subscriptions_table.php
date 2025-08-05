<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->enum('package_type', ['premium', 'prestige'])->nullable();
            $table->enum('timeline', ['12', '6', '1'])->nullable();
            $table->string('price')->nullable();
            $table->text('feature')->nullable();
            $table->integer('rewinds_limit')->nullable();
            $table->integer('swipes_limit')->nullable();

            $table->enum('status', ['active', 'inactive'])->default('active')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('subscriptions');
    }
};
