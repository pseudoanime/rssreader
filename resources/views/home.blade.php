@extends('layouts.app')

@section('content')
    @if (auth()->guest())
        {{-- expr --}}
    @else
        {{-- <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Dashboard</div>

                    <div class="panel-body">
                        You are logged in!
                    </div>
                </div>
            </div>
        </div> --}}
        <div class="row">
          <div class="col-sm-3 col-md-2 sidebar">
            <ul class="nav nav-sidebar">
                @foreach ($feeds as $feed)
                  <li><a href="{{env("APP_URL") . "/rss/" . $feed->id}}">{{$feed->name}} ({{$feed->unread}})</a></li>
                @endforeach
            </ul>
          </div>
          <div class="col-sm-9 col-md-10 main">
            @if(array_diff(array_column($items, 'id'),$read))
              <h4><a href = "{{Request::url() . "/read"}}" class="pull-right">Mark all as read </a> </h4>
              @foreach ($items as $item)
                @if(!in_array($item->id, $read))
                <br>
                  <div class="item">
                    <h2><a href="{{ $item->get_permalink() }}">{{ $item->get_title() }}</a></h2>
                    {!! $item->get_content() !!}
                    <p><small>Posted on {{ $item->get_date('j F Y | g:i a') }}</small></p>
                  </div>
                @endif
              @endforeach
            @else
                <div class="col-md-5 col-md-offset-4">You have no unread items for this feed.</div>
            @endif
          </div>
        </div>
    @endif
@endsection
