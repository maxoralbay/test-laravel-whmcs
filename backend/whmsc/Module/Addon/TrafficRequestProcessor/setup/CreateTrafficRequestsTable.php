<?php

use WHMCS\Module\Addon\TrafficRequestProcessor\Migrations\CreateTrafficRequestsTable;

/**
 * Run module migrations
 * @return void
 */
function traffic_request_schema()
{
    try {
        $migration = new CreateTrafficRequestsTable();
        $migration->up();
        return [
            'status' => 'success',
            'description' => 'TrafficReqest table created'
        ];
    } catch (\Exception $e) {
        return [
            'status' => 'error',
            'description' => 'Failed to create trafficReqest table: ' . $e->getMessage()
        ];
    }
}

/**
 * Remove module tables
 * @return array
 */
function traffic_request_drop_schema()
{
    try {
        $migration = new CreateTrafficRequestsTable();
        $migration->down();
        return [
            'status' => 'success',
            'description' => 'Traffic monitor tables removed successfully'
        ];
    } catch (\Exception $e) {
        return [
            'status' => 'error',
            'description' => 'Failed to remove traffic monitor tables: ' . $e->getMessage()
        ];
    }
}
