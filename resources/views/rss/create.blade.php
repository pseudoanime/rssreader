@extends("layouts.app")

@section("content")
    {{Form::open(["url" => "/rss"])}}
        @if (count($errors) > 0)
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="form-group">
            {{ Form::label("url", null, ['class' => 'control-label']) }}
            {{ Form::text("url", null, array_merge(['class' => 'form-control'])) }}
        </div>
        {{Form::submit()}}
    {{Form::close()}}
    <div class="table">
          @if (!$feeds->count())
              You do not have any feeds at the moment.
          @else
            <table class="table table-striped">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>url</th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody>
                      @foreach ($feeds as $key => $feed)
                        <tr>
                            <th scope="row">{{$key+1}}</th>
                            <td>{{$feed->url}}</td>
                            <td>
                                {{ Form::open(['url' => 'rss/' . $feed->id, 'method' => 'delete']) }}
                                    {{Form::submit("&#10006;", ["class" => "btn btn-link"])}}
                                {{ Form::close()}}
                            </td>
                        </tr>
                      @endforeach
                  </tbody>
            </table>
        @endif
    </div>
@endsection