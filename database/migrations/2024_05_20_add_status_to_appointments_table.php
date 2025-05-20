<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Appointment;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->string('status')->default(Appointment::STATUS_SCHEDULED)->after('reminder_minutes');
        });

        // Update existing appointments to have the default status
        DB::table('appointments')->update(['status' => Appointment::STATUS_SCHEDULED]);
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}; 