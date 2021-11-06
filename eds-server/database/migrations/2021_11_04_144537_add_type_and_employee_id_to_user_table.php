<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTypeAndEmployeeIdToUserTable extends Migration
{
    protected $tableName = 'users';

    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::table($this->tableName, function (Blueprint $table) {
            $table->tinyInteger('type', false, true)
                  ->nullable(false)
                  ->default(0)
                  ->after('id');

            $table->unsignedBigInteger('employee_id')
                  ->nullable(false)
                  ->unique()
                  ->after('id');
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn($this->tableName, 'type')) {
            Schema::table($this->tableName, function (Blueprint $table) {
                $table->dropColumn('type');
            });
        }
        if (Schema::hasColumn($this->tableName, 'employee_id')) {
            Schema::table($this->tableName, function (Blueprint $table) {
                $table->dropColumn('employee_id');
            });
        }
    }
}
