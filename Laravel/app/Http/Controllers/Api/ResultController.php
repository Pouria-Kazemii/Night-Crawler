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
        $isbn = $request->input('isbn');

        $numbers = $this->ConvertNumbers($isbn);

        $results = CrawlerResult::where('content.isbn', $numbers['persian'])
            ->orWhere('content.isbn', $numbers['english']) 
            ->get();
        
        return ResultResource::collection($results->load('crawler:title'));
    }


    function ConvertNumbers($string)
    {
        $persianNumbers = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $englishNumbers = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

        return [
            'english'=> str_replace($englishNumbers, $persianNumbers, $string),
            'persian' => str_replace($persianNumbers, $persianNumbers, $string)
        ];
    }
}
