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
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone_number')->unique()->nullable()->after('email');
            $table->string('profile_picture')->nullable()->after('phone_number');
            $table->timestamp('last_seen')->nullable()->after('profile_picture');
            $table->boolean('is_online')->default(false)->after('last_seen');
            
            // Add index for better performance
            $table->index('phone_number');
            $table->index('is_online');
            $table->index('last_seen');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['phone_number']);
            $table->dropIndex(['is_online']);
            $table->dropIndex(['last_seen']);
            
            $table->dropColumn([
                'phone_number',
                'profile_picture',
                'last_seen',
                'is_online'
            ]);
        });
    }
};