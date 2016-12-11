<?php

namespace App\Http\Controllers;

use Log;
use App\Feed;
use Illuminate\Http\Request;
use App\Http\Requests\AddFeedUrl;

class RssController extends Controller
{
    public function create()
    {
       Log::info(__METHOD__ . " : bof");

       $feeds = Feed::all();

       return view('rss.create')->with(compact('feeds'));
       
    }

    public function store(AddFeedUrl $request)
    {
        Log::info(__METHOD__ . " : bof");

        $user = auth()->user();

        $feed = new Feed;

        $feed->url = filter_var($request->url, FILTER_SANITIZE_STRING);

        $user->Feeds()->save($feed);

        return redirect("/rss/create");
    }
}
