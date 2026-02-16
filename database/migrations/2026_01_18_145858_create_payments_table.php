<?php

use App\Models\Admin;
use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('payments', function (Blueprint $table) {
      $table->id();
      $table->foreignIdFor(Employee::class);
      $table->foreignIdFor(Admin::class);
      $table->double('total_amount');
      $table->double('amount_paid');
      $table->boolean('is_paid');
      $table->timestamp('payment_date');
      $table->softDeletes();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('payments');
  }
};