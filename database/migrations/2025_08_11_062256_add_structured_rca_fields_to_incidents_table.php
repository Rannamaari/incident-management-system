<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('incidents', function (Blueprint $table) {
            // Add structured RCA fields for High severity incidents
            $table->text('corrective_actions')->nullable();
            $table->text('workaround')->nullable();
            $table->text('solution')->nullable();
            $table->text('recommendation')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incidents', function (Blueprint $table) {
            $table->dropColumn(['corrective_actions', 'workaround', 'solution', 'recommendation']);
        });
    }
};
