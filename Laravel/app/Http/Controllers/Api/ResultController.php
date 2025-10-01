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

    public function content(Request $request)
    {
        $isbn = $request->input('isbn');

        $numbers = $this->ConvertNumbers($isbn);

        $result = CrawlerResult::where(function ($q) use ($numbers) {
            $q->where('content.isbn', $numbers['persian'])
                ->orWhere('content.isbn', $numbers['english']);
        })->where('crawler_id', '68db8e7d4ef90d050505faac')
            ->pluck('content.picture')
            ->toArray();


        if ($result != []) {
            preg_match('/src="([^"]+)"/i', $result[0], $matches);
            if ($matches[1] != null) {
                $content = file_get_contents($matches[1]);
                return response($content , 200)->header('Content-Type', 'image/jpeg');
            }
        }

        return response()->json([
            'message' => 'not found',
            'data' => null,
            'status' => 404
        ]);
    }


    function ConvertNumbers($string)
    {
        $persianNumbers = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $englishNumbers = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

        return [
            'english' => str_replace($englishNumbers, $persianNumbers, $string),
            'persian' => str_replace($persianNumbers, $persianNumbers, $string)
        ];
    }
}
