<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->foreignId('current_organization_id')->nullable();
            $table->foreignId('current_connected_account_id')->nullable();
            $table->string('profile_photo_path', 2048)->nullable();
        });
    }
};
