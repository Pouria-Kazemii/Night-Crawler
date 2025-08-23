<?php

namespace App\Http\Controllers;

use App\Models\CrawlerJobSender;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CrawlerJobSenderController extends Controller
{
    public function index(): View
    {
        $senders = CrawlerJobSender::with(['crawler:_id,title'])
            ->orderBy('last_used_at', 'desc')->paginate(8);

        return view('sender.index', compact('senders'));
    }
}
