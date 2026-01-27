<?php

use App\Models\Employee;
use App\Models\Workshop;
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
        Schema::create('weekly_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Employee::class)->constrained();
            $table->foreignIdFor(Workshop::class)->constrained();
            $table->integer('week_number');
            $table->integer('month');
            $table->integer('year');
            $table->integer('amount_paid');
            $table->Boolean('is_paid');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weekly_histories');
    }
};
