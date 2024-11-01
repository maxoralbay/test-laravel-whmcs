<?php

namespace Tests\Feature;

class generateContentTest extends \PHPUnit\Framework\TestCase
{
    private $num = 100000;

    public function __construct__($num)
    {
        $this->num = $num;
    }

    public function init()
    {
        $data = [];
        for ($i = 0; $i < $this->num; $i++) {
            $data[] = ['key' => rand(1, 100), 'traffic' => rand(1000, 2000)];
        }
        return $data;
    }

    public function saveToFile($data)
    {
        $file = fopen("data.txt", "w");
        foreach ($data as $item) {
            fwrite($file, $item['key'] . '=>' . $item['traffic'] . ' ');
        }
        fclose($file);
    }

    public function test_generate()
    {
        $data = $this->init();
        assert(count($data) == $this->num);
        $this->saveToFile($data);
        $this->assertFileExists('data.txt');
    }
}

