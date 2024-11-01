<?php

namespace WHMCS\Module\Server;

use WHMCS\Database\Capsule;

/**
 * Traffic Request Processor for WHMCS
 */
class TrafficRequestProcessor
{
    /**
     * Database table name
     */
    private $table = 'mod_traffic_requests';

    /**
     * Constructor - creates table if not exists
     */
    public function __construct()
    {
        $this->createTableIfNotExists();
    }

    /**
     * Create the database table if it doesn't exist
     */
    private function createTableIfNotExists()
    {
        if (!Capsule::schema()->hasTable($this->table)) {
            Capsule::schema()->create($this->table, function ($table) {
                $table->integer('key')->unique();
                $table->bigInteger('traffic');
                $table->timestamps();
            });
        }
    }

    /**
     * Process raw traffic data string
     * @param string $rawData Raw traffic data string
     * @return array Processed and summed traffic data
     */
    public function processTrafficData(string $rawData): array
    {
        $rawData = $this->sanitizeInput($rawData);
        $data = $this->prepareData($rawData);
        $summedData = $this->sumTraffic($data);
        $this->saveToDatabase($summedData);

        return $summedData;
    }

    /**
     * Sanitize input string
     * @param string $input
     * @return string
     */
    private function sanitizeInput(string $input): string
    {
        // Remove potential SQL injection attempts
        $patterns = [
            '/(insert|update|delete|drop|truncate|create|alter|rename|replace|handler|load|outfile|dump)/i'
        ];
        return preg_replace($patterns, '', $input);
    }

    /**
     * Convert raw string data to array
     * @param string $rawData
     * @return array
     */
    private function prepareData(string $rawData): array
    {
        $pairs = explode(" ", trim($rawData));
        $data = [];
        foreach ($pairs as $pair) {
            $parts = explode("=>", $pair);
            if (count($parts) == 2 &&
                is_numeric($parts[0]) &&
                is_numeric($parts[1])) {
                $data[] = [
                    'key' => (int)$parts[0],
                    'traffic' => (int)$parts[1]
                ];
            }
        }
        return $data;
    }

    /**
     * Sum traffic for duplicate keys
     * @param array $data
     * @return array
     */
    private function sumTraffic(array $data): array
    {
        $summed = [];
        foreach ($data as $item) {
            $key = $item['key'];
            if (isset($summed[$key])) {
                $summed[$key] += $item['traffic'];
            } else {
                $summed[$key] = $item['traffic'];
            }
        }
        return $summed;
    }

    /**
     * Save processed data to database
     * @param array $data
     * @return int Number of records processed
     */
    private function saveToDatabase(array $data): int
    {
        $count = 0;
        foreach ($data as $key => $traffic) {
            try {
                Capsule::table($this->table)->updateOrInsert(
                    ['key' => $key],
                    [
                        'traffic' => $traffic,
                        'updated_at' => Capsule::raw('NOW()')
                    ]
                );
                $count++;
            } catch (\Exception $e) {
                logActivity("Traffic Processing Error: " . $e->getMessage());
            }
        }
        return $count;
    }

    /**
     * Get traffic data with pagination
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getTrafficData(int $limit = 100, int $offset = 0): array
    {
        return Capsule::table($this->table)
            ->orderBy('key')
            ->offset($offset)
            ->limit($limit)
            ->get()
            ->toArray();
    }
}
