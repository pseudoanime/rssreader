<?php

namespace App\Http\Controllers;

use Log;
use Feeds;
use App\Item;
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

                foreach ($items as &$item) {

                    if ($item->get_feed()->subscribe_url() == $url) { 

                      // dd(auth()->user()->readList->find($feedData->id)->pivot->count());

                        // $feedData->unread = $item->get_feed()->get_item_quantity();

                        $feedData->name = $item->get_feed()->get_title();

                        $feedRecord = Item::where('permalink', $item->get_permalink())->first();

                        if (is_null($feedRecord)) {

                            $newItem = new Item();

                            $newItem->permalink = $item->get_permalink();

                            $feedData->Items()->save($newItem);

                            $item->id = $newItem->id;

                            $feedData->unread++;

                        } else {

                            $item->id = $feedRecord->id;
                        }

                        $feedData->save();
                    }
                }
            }

            $feeds = $this->calculateUnreads(auth()->user()->Feedurls, $feed);

            $read = auth()->user()->readlist->pluck('id')->toArray();

            return view('home')->with(compact('items', 'feeds', 'read'));
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

        $feed = Feedurl::findOrFail($id);

        if ($feed->user_id == auth()->user()->id) {
            $feed->delete();

            return redirect()->back();
        }

        return "you do not have permission to delete this feed";
    }

    public function show($id, Request $request)
    {
        Log::info(__METHOD__ . " : bof");

        $read = auth()->user()->readlist->pluck('id')->toArray();

        $feed = Feeds::make(Feedurl::findOrFail($id)->url);

        $items = $feed->get_items();

        foreach ($items as &$item) {

              $feedRecord = Item::where('permalink', $item->get_permalink())->first();

              if (is_null($feedRecord)) {

                  $newItem = new Item();

                  $newItem->permalink = $item->get_permalink();

                  $feedUrl = Feedurl::findOrFail($id);

                  $feedUrl->Items()->save($newItem);

                  $feedUrl->unread++;

                  $feedUrl->save();

                  $item->id = $newItem->id;

              } else {

                  $item->id = $feedRecord->id;
              }
        }

        $feeds = $this->calculateUnreads(auth()->user()->Feedurls, $feed);

        return view('home')->with(compact('items', 'feeds', 'read'));
    }

    public function markAllRead($id, Request $request)
    {
        Log::info(__METHOD__ . " : bof");

        $user = auth()->user();

        $readItems= Feedurl::findOrFail($id)->Items;

        foreach ($readItems as $item) {
            
            $user->readList()->attach($item->id);

        }

        return redirect()->back();

    }

    public function calculateUnreads($feedurls, $feedContent)
    {
        Log::info(__METHOD__ . " : bof");

        $user = auth()->user();

        $this->cleanUpFeeds($feedContent);

        foreach ($feedContent->get_items() as $item) {

            $feedItem[$item->get_feed()->subscribe_url()] = isset($feedItem[$item->get_feed()->subscribe_url()]) ? $feedItem[$item->get_feed()->subscribe_url()]+1 :1;
        }

        foreach ($feedurls as $key => $feed) {

            $readUrls = $user->readList()->where('feedurl_id', $feed->id)->get();

            if(isset($feedItem[$feed->url])) {
                
                $feed->unread = $feedItem[$feed->url] - count($readUrls);

                $feed->save();

            }
        }

        return $feedurls;
    }

    public function cleanUpFeeds($feedContent)
    {
        Log::info(__METHOD__ . " : bof");

        $itemsList = [];

        foreach ($feedContent->get_Items() as $key => $item) {
            
            if(null !== $item->get_feed()->subscribe_url()) {
                $itemList [$item->get_feed()->subscribe_url()] = Item::whereHas('Feedurl', function($query) use($item) {
                    $query->where('url', $item->get_feed()->subscribe_url());
                })->get()->pluck('permalink', 'id')->toArray();
            }

           unset($itemList[$item->get_feed()->subscribe_url()][array_search($item->get_permalink(), $itemList[$item->get_feed()->subscribe_url()])]);

        }

        if(count($itemsList)) {

            dd($itemList);

            foreach ($itemList as $feedname) {
    
                foreach ($feedname as $key => $permalink) {
                    
                    Item::findOrFail($key)->delete();
                }
            }
        }
    }
}
