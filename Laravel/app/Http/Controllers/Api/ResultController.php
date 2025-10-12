<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ResultResource;
use App\Models\CrawlerJobSender;
use App\Models\CrawlerResult;
use Illuminate\Http\Request;

class ResultController extends Controller
{
    public function index(Request $request)
    {
        $isbn = $request->input('isbn', false);
        $crawler = $request->input('crawler_id', false);
        $url = $request->input('url', false);
        $per_page = $request->input('per_page', 50);

        $results =  CrawlerResult::query();

        if ($isbn) {

            $numbers = $this->ConvertNumbers($isbn);

            $results->where(function ($q) use ($numbers) {
                $q->where('content.isbn', $numbers['persian'])
                    ->orWhere('content.isbn', $numbers['english']);
            });
        } elseif ($crawler) {
            $jobs = CrawlerJobSender::where('crawler_id', $crawler)->where('step', '!=', (int)1);
            $jobs_id = $jobs->pluck('id')->toArray();
            $results->whereIn('crawler_job_sender_id', $jobs_id);
        } elseif ($url) {
            $results->Where('final_url', $url);
        }

        return ResultResource::collection($results->paginate($per_page)->load('crawler:title'));
    }

    public function image(Request $request)
    {
        $isbn = $request->input('isbn', false);
        $url = $request->input('url', false);

        if ($isbn) {
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
        } else if ($url) {
            $content = file_get_contents($url);
            return response($content, 200)->header('Content-Type', 'image/jpeg');
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
