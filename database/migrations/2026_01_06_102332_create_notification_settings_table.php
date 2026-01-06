<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('notification_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('boolean');
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Seed initial setting (disabled by default)
        DB::table('notification_settings')->insert([
            'key' => 'auto_send_enabled',
            'value' => 'false',
            'type' => 'boolean',
            'description' => 'Automatically send email notifications for new incidents with 5-minute delay',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_settings');
    }
};
