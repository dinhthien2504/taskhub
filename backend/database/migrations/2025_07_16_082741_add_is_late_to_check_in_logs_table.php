<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('check_in_logs', function (Blueprint $table) {
            $table->boolean('is_late')->default(false);
            $table->integer('late_by_minutes')->nullable();
        });
    }

    public function down()
    {
        Schema::table('check_in_logs', function (Blueprint $table) {
            $table->dropColumn('is_late');
            $table->dropColumn('late_by_minutes');
        });
    }

};
