<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Shieldforce\FederalFilamentStore\Enums\StatusCartEnum;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ffs_carts', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->string('identifier');

            $table->uuid('uuid')->nullable();

            $table->integer('status')
                ->default(StatusCartEnum::comprando->value);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ffs_carts');
    }
};
