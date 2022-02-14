<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\PenaltyStatus;

class CreatePenaltiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('penalties', function (Blueprint $table) {
            $table->id();
            $table->uuid('ref_id');
            $table->foreignId('user_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('loan_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->enum('status', array_keys(PenaltyStatus::$list))->default(PenaltyStatus::OPEN_KEY)->nullable()->comment(json_encode(PenaltyStatus::$list, JSON_PRETTY_PRINT));
            $table->double('amount', 8, 2);
            $table->text('comment')->nullable();
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
        Schema::dropIfExists('penalties');
    }
}
