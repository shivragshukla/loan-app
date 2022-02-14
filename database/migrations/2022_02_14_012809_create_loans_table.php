<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Status;

class CreateLoansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->uuid('ref_id');
            $table->foreignId('user_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('loan_term_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedBigInteger('manager_id')->nullable();
            $table->foreign('manager_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('set null');
            $table->double('amount', 8, 2);
            $table->enum('status', array_keys(Status::$list))->default(Status::PENDING_KEY)->nullable()->comment(json_encode(Status::$list, JSON_PRETTY_PRINT));
            $table->text('comment')->nullable();
            $table->date('start_repay_date')->nullable();
            $table->date('next_repay_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('loans');
    }
}
