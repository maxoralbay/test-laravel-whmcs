<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReqProccessController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('req_proccess.index');
    }

    /***
     * Sum request traffic if duplicate key
     * @param array $data
     * @return array
     */
    public function sumReqTraffic(array $data): array
    {
        $resultData = [];
        array_map(function ($item) use (&$resultData) {
            if (isset($resultData[$item['key']])) {
                $resultData[$item['key']] += $item['traffic'];
            } else {
                $resultData[$item['key']] = $item['traffic'];
            }
        }, $data);
        return $resultData;
    }

    /***
     * Save request traffic to db
     * @param array $data
     * @return int
     */
    public function saveReqTraffic(array $data): int
    {
        $result = [];
        foreach ($data as $key => $value) {
            $result[] = ['key' => $key, 'traffic' => $value];
        }
        if (count($result) > 0)
            \App\Models\ReqProccessModel::upsert($result, ['key'], ['traffic']);
        return count($result);

    }

    /***
     * Prepare data
     * @param string $raw_data
     * @return array
     */
    public function prepareData(string $raw_data): array
    {
        $pairs = explode(" ", $raw_data);
        $data = [];
        foreach ($pairs as $pair) {
            // Split the pair into key and value
            $parts = explode("=>", $pair);
            // if key and value are not empty and index exists
            if (count($parts) == 2 && !empty($parts[0]) && !empty($parts[1])) {
                // Add the key and value to the result
                $data[] = ['key' => $parts[0], 'traffic' => $parts[1]];
            }
        }
        return $data;
    }

    /***
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function proccessReqTraffic(Request $request): \Illuminate\Http\JsonResponse
    {
        // raw text format in the body
        $raw_data = $request->getContent();
        // sanitize data
        // sql injection
        $raw_data = preg_replace('/(insert|update|delete|drop|truncate|create|alter|rename|replace|handler|load|outfile|dump)/i', '', $raw_data);
        // convert string to array
        $data = $this->prepareData($raw_data);
        $result = $this->sumReqTraffic($data);
        $result_db = $this->saveReqTraffic($result);
        return response()->json(['status' => 'success', 'data' => $result_db]);

    }
}
