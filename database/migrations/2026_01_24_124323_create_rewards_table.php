<?php

use App\Models\Admin;
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
    Schema::create('rewards', function (Blueprint $table) {
      $table->id();
      $table->foreignIdFor(Employee::class)->constrained();
      $table->foreignIdFor(Admin::class)->constrained();
      $table->decimal('amount', 8, 2);
      $table->string('reason');
      $table->date('date_issued');
      $table->softDeletes();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('rewards');
  }
};