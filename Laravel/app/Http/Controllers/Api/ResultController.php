<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ResultResource;
use App\Models\CrawlerResult;
use Illuminate\Http\Request;

class ResultController extends Controller
{
    public function index(Request $request)
    {
        $per_page = $request->input('per_page',100);
        $id = $request->input('sender_id' , '68ced193744d1602190facc8');
        $results = CrawlerResult::where('crawler_job_sender_id', $id)->paginate($per_page);
        return ResultResource::collection($results);
    }
}
