@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Links</div>

                    <div class="panel-body">
                        <ul>
                            <li><a href="{{ route('bot-config') }}">Bot Config</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
