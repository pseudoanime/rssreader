<?php

namespace App\Http\Controllers;

use Log;
use App\Feedurl;
use Illuminate\Http\Request;
use App\Http\Requests\AddFeedUrl;

class RssController extends Controller
{
    public function create()
    {
       Log::info(__METHOD__ . " : bof");

       $feeds = Feedurl::all();

       return view('rss.create')->with(compact('feeds'));
       
    }

    public function store(AddFeedUrl $request)
    {
        Log::info(__METHOD__ . " : bof");

        $user = auth()->user();

        $feed = new Feedurl;

        $feed->url = filter_var($request->url, FILTER_SANITIZE_STRING);

        $user->Feedurls()->save($feed);

        return redirect("/rss/create");
    }

    public function destroy($id, Request $request)
    {
       Log::info(__METHOD__ . " : bof");

       $feed = Feed::findOrFail($id);

       if ($feed->user_id == auth()->user()->id) {
           
           $feed->delete();

           return redirect()->back();
       }

       return "you do not have permission to delete this feed";


    }
}
