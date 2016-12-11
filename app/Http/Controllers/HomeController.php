<?php

namespace App\Http\Controllers;

use Feeds;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (auth()->check()) {
            
            $feeds = auth()->user()->Feedurls;

            if (!is_null($feeds)) {

                $feedUrls = [];

                foreach ($feeds as $feed) {

                    $feedUrls[$feed->id] = $feed->url;
                }

                $feed = Feeds::make($feedUrls,5);

                $data = array(
                  'title'     => $feed->get_title(),
                  'permalink' => $feed->get_permalink(),
                  'items'     => $feed->get_items(),
                );

                return view('home')->with(compact('data', 'feedUrls'));
            }
        }

        return view('home');
    }
}
