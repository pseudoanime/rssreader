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
        <?php
         // var_dump($data);
        ?>
        <div class="row">
          <div class="col-sm-3 col-md-2 sidebar">
            <ul class="nav nav-sidebar">
               {{--  @foreach ($feedUrls as $feed)
                  <li>{{$feed}}</li>
                @endforeach --}}
            </ul>
          </div>
          <div class="col-sm-9 col-md-10 main">
             <div class="header">
              <h1><a href="{{ $data["permalink"] }}">{{ $data["title"] }}</a></h1>
            </div>

            @foreach ($data["items"] as $item)
              <div class="item">
                <h2><a href="{{ $item->get_permalink() }}">{{ $item->get_title() }}</a></h2>
                {!! $item->get_content() !!}
                <p><small>Posted on {{ $item->get_date('j F Y | g:i a') }}</small></p>
              </div>
            @endforeach
          </div>
        </div>
    @endif
@endsection
