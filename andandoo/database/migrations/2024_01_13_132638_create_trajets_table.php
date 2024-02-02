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
        Schema::create('trajets', function (Blueprint $table) {
            $table->id();
            $table->string('LieuDepart');
            $table->string('LieuArrivee');
            $table->date('DateDepart');
            $table->time('HeureD');
            $table->float('Prix');
            $table->string('DescriptionTrajet');
            $table->enum('Status', ['terminee', 'enCours'])->default('enCours');
            $table->foreignId('voiture_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trajets');
    }
};
