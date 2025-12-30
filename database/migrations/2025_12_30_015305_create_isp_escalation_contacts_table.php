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
        Schema::create('isp_escalation_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('isp_link_id')->constrained('isp_links')->onDelete('cascade');
            $table->enum('escalation_level', ['L1', 'L2', 'L3']);
            $table->string('contact_name', 100);
            $table->string('contact_phone', 50);
            $table->string('contact_email', 100)->nullable();
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('isp_escalation_contacts');
    }
};
