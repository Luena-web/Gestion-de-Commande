<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('telephone', 20)->nullable()->change();
            $table->string('google_id')->nullable()->unique();
        });
    }

    public function down(): void
    {
        if (DB::table('users')->whereNull('telephone')->exists()) {
            throw new RuntimeException(
                'Cannot roll back Google identity migration while users have no telephone number.'
            );
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['google_id']);
            $table->dropColumn('google_id');
            $table->string('telephone', 20)->nullable(false)->change();
        });
    }
};
