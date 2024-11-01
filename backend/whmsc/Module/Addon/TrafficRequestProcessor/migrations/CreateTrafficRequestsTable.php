<?php

namespace WHMCS\Module\Addon\TrafficRequestProcessor\Migrations;

use WHMCS\Database\Capsule;
use WHMCS\Database\Schema\Blueprint;

class CreateTrafficRequestsTable
{
    /**
     * Run the migration
     * @return void
     */
    public function up()
    {
        if (!Capsule::schema()->hasTable('mod_traffic_requests')) {
            Capsule::schema()->create('mod_traffic_requests', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('key')->unique()->index();
                $table->bigInteger('traffic')->default(0);
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrent();
            });
        }
    }

    /**
     * Reverse the migration
     * @return void
     */
    public function down()
    {
        Capsule::schema()->dropIfExists('mod_traffic_requests');
    }
}
