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
            $table->foreignId('company_id')->nullable()->after('id')->constrained()->onDelete('cascade');
             $table->foreignId('invited_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('invitation_token')->nullable()->unique();
            $table->enum('invitation_status', ['0', '1'])->default('0')->comment('0=pending, 1=accepted');
            $table->timestamp('invited_at')->nullable();
            $table->boolean('is_active')->default(true)->after('remember_token');
            $table->index('company_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn(['company_id', 'is_active']);
            $table->dropForeign(['invited_by']);
            $table->dropColumn(['invited_by', 'invitation_token', 'invitation_status', 'invited_at']);
        });
    }
};
