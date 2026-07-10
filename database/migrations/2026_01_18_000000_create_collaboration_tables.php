<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Notification Preferences
        Schema::create('notification_preferences', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->boolean('email_notifications')->default(true);
            $table->boolean('push_notifications')->default(true);
            $table->boolean('in_app_notifications')->default(true);
            $table->text('alert_categories'); // JSON of enabled categories (Shipment, Weather, etc.)
            $table->timestamps();
        });

        // Notification Logs (Audit Trail for notifications)
        Schema::create('notification_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('notification_id')->nullable(); // Link to Laravel's notifications table if needed
            $table->string('type'); // Email, Push, In-App
            $table->string('status'); // Sent, Failed, Read
            $table->timestamps();
        });

        // Activities (Audit Trail/Activity Feed)
        Schema::create('activities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('action'); // Updated, Created, Deleted
            $table->string('subject_type')->nullable(); // Model class
            $table->string('subject_id')->nullable(); // Model ID
            $table->text('description')->nullable();
            $table->text('properties')->nullable(); // JSON payload
            $table->timestamps();
        });

        // Comments (Collaboration)
        Schema::create('comments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('commentable_type'); // Morph to Shipment, Commodity, Task
            $table->string('commentable_id');
            $table->text('body');
            $table->text('attachments')->nullable(); // JSON of file paths
            $table->timestamps();
        });

        // Tasks (Task Management)
        Schema::create('tasks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignUuid('created_by')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('priority')->default('Medium'); // Low, Medium, High, Critical
            $table->string('status')->default('Pending'); // Pending, In Progress, Completed, Cancelled
            $table->date('due_date')->nullable();
            $table->string('related_entity_type')->nullable(); // Morph (Shipment, Document)
            $table->string('related_entity_id')->nullable();
            $table->timestamps();
        });

        // Task Assignments
        Schema::create('task_assignments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('task_id')->constrained('tasks')->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamp('assigned_at');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        // Approvals (Generic Approval Workflow Engine)
        Schema::create('approvals', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('approvable_type'); // Morph (RedirectRequest, Document, Payment)
            $table->string('approvable_id');
            $table->string('status')->default('Pending'); // Pending, Approved, Rejected
            $table->integer('required_approvals')->default(1);
            $table->integer('current_approvals')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Approval Logs
        Schema::create('approval_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('approval_id')->constrained('approvals')->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('action'); // Approved, Rejected
            $table->text('comments')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('approval_logs');
        Schema::dropIfExists('approvals');
        Schema::dropIfExists('task_assignments');
        Schema::dropIfExists('tasks');
        Schema::dropIfExists('comments');
        Schema::dropIfExists('activities');
        Schema::dropIfExists('notification_logs');
        Schema::dropIfExists('notification_preferences');
    }
};
