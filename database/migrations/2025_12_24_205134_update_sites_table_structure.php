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
        Schema::table('sites', function (Blueprint $table) {
            // Drop old columns
            $table->dropColumn([
                'site_id',
                'atoll_code',
                'site_name',
                'coverage',
                'operational_date',
                'transmission_or_backhaul',
                'remarks',
                'status',
                'review_date',
                'created_by',
                'updated_by',
            ]);

            // Add new columns
            $table->foreignId('region_id')->after('id')->constrained()->onDelete('cascade');
            $table->foreignId('location_id')->after('region_id')->constrained()->onDelete('cascade');
            $table->string('site_number')->after('location_id');
            $table->string('site_code')->unique()->after('site_number');
            $table->string('display_name')->after('site_code');
            $table->boolean('is_active')->default(true)->after('display_name');

            $table->index(['region_id', 'location_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sites', function (Blueprint $table) {
            // Drop new columns
            $table->dropForeign(['region_id']);
            $table->dropForeign(['location_id']);
            $table->dropIndex(['region_id', 'location_id']);
            $table->dropColumn([
                'region_id',
                'location_id',
                'site_number',
                'site_code',
                'display_name',
                'is_active',
            ]);

            // Add back old columns
            $table->string('site_id')->unique();
            $table->string('atoll_code')->index();
            $table->string('site_name')->index();
            $table->string('coverage');
            $table->date('operational_date')->index();
            $table->string('transmission_or_backhaul');
            $table->text('remarks')->nullable();
            $table->enum('status', ['Active', 'Monitoring', 'Maintenance', 'Inactive'])->default('Active')->index();
            $table->date('review_date')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
        });
    }
};
