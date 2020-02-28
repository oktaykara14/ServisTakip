@extends('layout.master')

@section('page-title')
    <div class="page-title">
        <h1>Yetkisiz Sayfa <small></small></h1>
    </div>
@stop

@section('page-plugins')
@stop

@section('page-styles')
    <link href="{{ URL::to('assets/admin/pages/css/tasks.css') }}" rel="stylesheet" type="text/css"/>
@stop

@section('page-js')
@stop

@section('scripts')
    <script>
        jQuery(document).ready(function() {
            Metronic.init(); // init metronic core componets
            Layout.init(); // init layout
            Demo.init(); // init demo features
            QuickSidebar.init(); // init quick sidebar
//   Index.init(); // init index page
        });
    </script>
@stop

@section('content')
<h1>Create a new message</h1>
{{Form::open(['route' => 'messages.store'])}}
<div class="col-md-6">
    <!-- Subject Form Input -->
    <div class="form-group">
        {{ Form::label('subject', 'Subject', ['class' => 'control-label']) }}
        {{ Form::text('subject', null, ['class' => 'form-control']) }}
    </div>

    <!-- Message Form Input -->
    <div class="form-group">
        {{ Form::label('message', 'Message', ['class' => 'control-label']) }}
        {{ Form::textarea('message', null, ['class' => 'form-control']) }}
    </div>

    @if($users->count() > 0)
    <div class="checkbox">
        @foreach($users as $user)
            <label title="{{$user->adi_soyadi}}"><input type="checkbox" name="recipients[]" value="{{$user->id}}">{{$user->adi_soyadi}}</label>
        @endforeach
    </div>
    @endif
    
    <!-- Submit Form Input -->
    <div class="form-group">
        {{ Form::submit('Submit', ['class' => 'btn btn-primary form-control']) }}
    </div>
</div>
{{Form::close()}}
@stop
