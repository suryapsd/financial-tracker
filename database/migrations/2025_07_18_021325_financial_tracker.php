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
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name'); // e.g. BCA, Dana, Gopay
            $table->string('account_number')->nullable();
            $table->enum('account_type', ['bank', 'ewallet', 'cash', 'crypto']);
            $table->string('institution')->nullable();
            $table->decimal('balance', 18, 2)->default(0);
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['income', 'expense', 'asset', 'debt', 'emergency', 'dream']);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('incomes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('account_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null');
            $table->string('source');
            $table->decimal('amount', 15, 2);
            $table->date('received_at');
            $table->timestamps();
        });

        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('account_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null');
            $table->string('name');
            $table->decimal('amount', 15, 2);
            $table->date('expense_date');
            $table->enum('frequency', ['daily', 'weekly', 'monthly', 'yearly', 'once'])->nullable();
            $table->timestamps();
        });

        Schema::create('current_salaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('account_id')->constrained('accounts')->onDelete('cascade');
            $table->decimal('amount', 15, 2);
            $table->string('income_source')->nullable();
            $table->date('effective_date')->nullable();
            $table->timestamps();
        });

        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('account_id')->nullable()->constrained('accounts')->onDelete('set null');
            $table->string('name');
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null');
            $table->string('institution')->nullable();
            $table->decimal('value', 18, 2);
            $table->date('acquired_date')->nullable();
            $table->timestamps();
        });

        Schema::create('debts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null');
            $table->string('creditor');
            $table->decimal('amount', 15, 2);
            $table->decimal('monthly_payment', 15, 2)->nullable();
            $table->date('due_date');
            $table->timestamps();
        });

        Schema::create('emergency_funds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null');
            $table->decimal('target_amount', 18, 2);
            $table->decimal('current_amount', 18, 2);
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('financial_goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->decimal('target_amount', 15, 2);
            $table->decimal('current_amount', 15, 2)->default(0);
            $table->date('deadline')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('savings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('goal_id')->constrained('financial_goals')->onDelete('cascade');
            $table->decimal('amount', 15, 2);
            $table->date('saved_date');
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('budget_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null');
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('planned_amount', 15, 2);
            $table->decimal('actual_amount', 15, 2)->default(0);
            $table->unsignedTinyInteger('month');
            $table->unsignedSmallInteger('year');
            $table->timestamps();
        });

        Schema::create('dreams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('set null');
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('target_amount', 15, 2);
            $table->decimal('saved_amount', 15, 2)->default(0);
            $table->date('target_date')->nullable();
            $table->timestamps();
        });

        Schema::create('financial_health_checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('score');
            $table->string('level');
            $table->text('recommendations')->nullable(); // JSON string/array if needed
            $table->timestamps();
        });

        Schema::create('financial_levels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedTinyInteger('level'); // 0–5, misalnya
            $table->integer('score')->default(0);
            $table->string('description');
            $table->timestamps();
        });

        Schema::create('financial_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedTinyInteger('month');
            $table->unsignedSmallInteger('year');
            $table->decimal('total_income', 15, 2);
            $table->decimal('total_expense', 15, 2);
            $table->decimal('net_worth', 15, 2);
            $table->decimal('cash_flow', 15, 2);
            $table->decimal('asset_value', 15, 2);
            $table->decimal('debt_value', 15, 2);
            $table->timestamps();
        });

        Schema::create('recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['budgeting', 'debt', 'asset', 'saving', 'health']);
            $table->text('recommendation');
            $table->timestamps();
        });

        Schema::create('motivations', function (Blueprint $table) {
            $table->id();
            $table->string('category')->nullable(); // e.g. saving, investing, debt
            $table->text('quote');
            $table->string('author')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('incomes');
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('current_salaries');
        Schema::dropIfExists('assets');
        Schema::dropIfExists('debts');
        Schema::dropIfExists('emergency_funds');
        Schema::dropIfExists('financial_goals');
        Schema::dropIfExists('savings');
        Schema::dropIfExists('budget_plans');
        Schema::dropIfExists('dreams');
        Schema::dropIfExists('financial_health_checks');
        Schema::dropIfExists('financial_levels');
        Schema::dropIfExists('financial_reports');
        Schema::dropIfExists('recommendations');
        Schema::dropIfExists('motivations');
    }
};
