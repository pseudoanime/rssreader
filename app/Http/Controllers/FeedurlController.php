<?php

namespace App\Http\Controllers;

use Log;
use App\Feedurl;
use Illuminate\Http\Request;

class FeedurlController extends Controller
{
    public function markAllRead()
    {
        Log::info(__METHOD__ . " : bof");

        $user = auth()->user();

        foreach (Feedurl::all() as $feed) {

            $feed->unread = 0;

            $feed->save();
           
            foreach ($feed->Items as $item) {

                $user->readList()->attach($item->id);

            }
        
        }

        return redirect()->back();

    }
}
