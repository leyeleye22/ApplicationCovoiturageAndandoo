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
        Schema::create('utilisateurs', function (Blueprint $table) {
            $table->id();
            $table->string('Nom');
            $table->string('Prenom');
            $table->string('Email');
            $table->string('Telephone');
            $table->string('ImageProfile')->nullable();
            $table->string('PermisConduire')->nullable();
            $table->string('CarteGrise')->nullable();
            $table->string('Licence')->nullable();
            $table->enum('role', ['chauffeur', 'client']);
            $table->foreignId('zone_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->boolean('TemporaryBlock')->default(false);
            $table->boolean('PermanentBlock')->default(false);
            $table->string('password');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('utilisateurs');
    }
};
