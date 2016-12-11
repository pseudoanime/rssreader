<?php

namespace App\Http\Controllers;

use Log;
use Feeds;
use App\Feedurl;
use Illuminate\Http\Request;
use App\Http\Requests\AddFeedUrl;

class RssController extends Controller
{
    public function index()
    {
        Log::info(__METHOD__ . " : bof");

        $feeds = auth()->user()->Feedurls;

        if (!is_null($feeds)) {
            $feedUrls = [];

            foreach ($feeds as $feed) {
                $feedUrls[$feed->id] = $feed->url;
            }

            $feed = Feeds::make($feedUrls);

            $items = $feed->get_items();

            foreach ($feeds as $key => $feedData) {
                $url = $feedData->url;

                foreach ($items as $item) {
                    if ($item->get_feed()->subscribe_url() == $url) {
                        $feedUrls[$feedData->id] = $item->get_feed()->get_title() . "(" . $item->get_feed()->get_item_quantity() . ")" ;

                        break;
                    }
                }
            }

            return view('home')->with(compact('items', 'feedUrls'));
        }
    }
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
