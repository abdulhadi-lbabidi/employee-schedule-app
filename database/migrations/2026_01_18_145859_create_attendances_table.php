<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Employee;
use App\Models\Workshop;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Employee::class)->constrained();
            $table->foreignIdFor(Workshop::class)->constrained();
            $table->date('date');
            $table->timestamp('check_in');
            $table->timestamp('check_out')->nullable();
            $table->integer('week_number');
            $table->text('note')->nullable();
            $table->double('regular_hours')->default(0);
            $table->double('overtime_hours')->default(0);
            $table->enum('status', ['مؤرشف', 'قيد الرفع']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
