<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ReqProccessControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function read_from_file($file): array
    {
        $data = [];
        $file = fopen($file, 'r');
        while ($line = fgets($file)) {
            $line = explode('=>', $line);
            $data[] = ['key' => $line[0], 'traffic' => $line[1]];
        }
        fclose($file);
        return $data;
    }

    public function test_proccessReqTraffic(): void
    {
        // read from file data.txt
        $test_file = 'data.txt';
        $file_data = $this->read_from_file($test_file);
        $response = $this->post('/api/proccessReqTraffic', ['data' => $file_data]);
        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);
    }
}
