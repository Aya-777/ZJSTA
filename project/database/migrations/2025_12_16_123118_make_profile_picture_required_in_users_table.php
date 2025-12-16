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
      $defaultAvatarPath = 'avatars/default_avatar.png';
      Schema::table('users', function (Blueprint $table) use ($defaultAvatarPath) {
            $table->string('profile_picture')->default($defaultAvatarPath)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
      Schema::table('users', function (Blueprint $table) {
            $table->string('profile_picture')->nullable()->default(null)->change();
        });
    }
};
