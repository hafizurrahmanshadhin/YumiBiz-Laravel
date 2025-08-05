<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('business_experiences', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            $table->unsignedBigInteger('meta_id')->nullable();
            $table->foreign('meta_id')->references('id')->on('metas')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            $table->string('industry')->nullable();
            $table->string('other_industry')->nullable();
            $table->string('years_of_experience')->nullable();
            $table->string('areas_of_expertise')->nullable();
            $table->string('other_expertise')->nullable();
            $table->string('support_offer')->nullable();
            $table->string('other_support_offer')->nullable();

            $table->string('designation', 100)->nullable();
            $table->string('company_name', 150)->nullable();
            $table->string('experience_from')->nullable();
            $table->string('experience_to')->nullable();

            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('business_experiences');
    }
};
