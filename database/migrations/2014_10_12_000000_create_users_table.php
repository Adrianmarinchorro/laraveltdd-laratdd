<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id'); // Integer Unsigned Autoincrement
            $table->string('name'); // Varchar
            $table->string('email')->unique(); // Varchar y Unique
            $table->string('password'); // Varchar
            $table->string('role');
            $table->rememberToken(); // metodo helper para columna comun en la app como ej almacenar token para recordar usuarios.
            $table->timestamps(); // helper que genera las columnas updated_at y created_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
