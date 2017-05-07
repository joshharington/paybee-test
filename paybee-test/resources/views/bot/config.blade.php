@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <div class="panel panel-default">
                    <div class="panel-heading">Links</div>

                    <div class="panel-body">
                        <ul>
                            <li><a href="{{ route('bot-config') }}">Bot Config</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="panel panel-default">
                    <div class="panel-heading">Change the Currency</div>

                    <div class="panel-body">
                        <div>
                            <select class="form-control" id="currency-select">
                                @foreach($currencies as $index => $currency)
                                    <option value="{{ $currency['currency'] }}" {{ ($default_currency == $currency['currency']) ? 'selected' : '' }}>{{ $currency['text'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="clearfix"></div>
                        <br />
                        <button type="button" class="btn btn-block btn-primary" id="btn-update-currency">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('custom-scripts')
    <script src="{{ asset('/js/notify.min.js') }}"></script>
    <script>
        $(document).ready(function() {

            var default_currency = '{{ $default_currency }}';

            $(document).on('click', '#btn-update-currency', function() {
                update();
            });

            $(document).on('change', '#currency-select', function() {
                default_currency = $(this).val();
            });

            function update() {
                $.notify("Updating", "info");

                $.ajax({
                    url: '/bot-config',
                    method: 'POST',
                    data: {currency: default_currency, _token: window.Laravel.csrfToken},
                    success: function (res) {
                        $.notify(res, "success");
                    },
                    error: function(res) {
                        $.notify(res, "error");
                    }
                });
            }

        });
    </script>
@endsection