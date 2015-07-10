<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAdminCohortIdUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_admin')->default(false);
            $table->integer('cohort_id')->unsigned()->nullable();

            $table->foreign('cohort_id')->references('id')->on('cohorts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign('users_cohort_id_foreign');

            $table->dropColumn('cohort_id');
            $table->dropColumn('is_admin');
        });
    }
}
