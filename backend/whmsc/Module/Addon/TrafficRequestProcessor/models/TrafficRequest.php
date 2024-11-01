<?php

namespace WHMCS\Module\Addon\TrafficMonitor\Models;

use WHMCS\Model\AbstractModel;

/**
 * Traffic Request Model
 */
class TrafficRequest extends AbstractModel
{
    protected $table = 'mod_traffic_requests';
    protected $fillable = [
        'key',
        'traffic',
    ];
    public $timestamps = true;
}
