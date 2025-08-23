<?php

namespace App\Http\Controllers;

use App\Models\CrawlerResult;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CrawlerResultController extends Controller
{
    public function index() : View
    {
        $results=CrawlerResult::with(['crawler:_id,title'])
        ->orderBy('updated_at' , 'desc')->paginate(15);

        return view('result.index' , compact('results'));
    }
}
