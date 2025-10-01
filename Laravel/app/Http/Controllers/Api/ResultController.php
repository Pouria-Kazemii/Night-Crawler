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
        $isbn = $request->input('isbn', false);
        $crawler = $request->input('crawler_id', false);
        $per_page = $request->input('per_page', 50);

        $results =  CrawlerResult::query();

        if ($isbn) {
            $numbers = $this->ConvertNumbers($isbn);

            $results->where('content.isbn', $numbers['persian'])
                ->orWhere('content.isbn', $numbers['english']);
        }

        if ($crawler) {
            $results->where('crawler_id', $crawler);
        }
        
        if ($results->count() != 1) {
            return ResultResource::collection($results->paginate($per_page)->load('crawler:title'));
        } else {
            return ResultResource::make($results->paginate()->load('crawler:title'));
        }
    }

    public function image(Request $request)
    {
        $isbn = $request->input('isbn');

        $numbers = $this->ConvertNumbers($isbn);

        $result = CrawlerResult::where(function ($q) use ($numbers) {
            $q->where('content.isbn', $numbers['persian'])
                ->orWhere('content.isbn', $numbers['english']);
        })->where('crawler_id', '68db8e7d4ef90d050505faac')
            ->pluck('content.image')
            ->toArray();


        if ($result != [] and !empty($result)) {
            preg_match('/src="([^"]+)"/i', $result[0][0], $matches);

            if ($matches[1] != null) {
                $content = file_get_contents($matches[1]);
                return response($content, 200)->header('Content-Type', 'image/jpeg');
            }
        } else {
            return response()->json([
                'message' => 'not found',
                'data' => null,
                'status' => 404
            ]);
        }
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
