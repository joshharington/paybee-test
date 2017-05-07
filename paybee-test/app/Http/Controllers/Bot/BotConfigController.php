<?php

namespace App\Http\Controllers\Bot;

use App\BotSetting;
use App\Http\Requests\UpdateCurrencyRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BotConfigController extends Controller {

    public function index() {
        $endpoint = 'http://api.coindesk.com/v1/bpi/supported-currencies.json';
        $data = file_get_contents($endpoint);
        $data = json_decode($data);

        $currencies = [];

        foreach($data as $index => $loc) {
            $currencies[] = ['currency' => $loc->currency, 'text' => '(' . $loc->currency . ') ' . $loc->country, 'country' => $loc->country];
        }

        $default_currency = BotSetting::where('key', 'default.currency')->first();
        if(!$default_currency) {
            $default_currency = 'USD';
        } else {
            $default_currency = $default_currency->value;
        }


        return view('bot.config', ['currencies' => $currencies, 'default_currency' => $default_currency]);
    }

    public function update(UpdateCurrencyRequest $request) {

        $currency_input = $request->currency;

        $currency = BotSetting::firstOrNew(['key' => 'default.currency']);
        $currency->value = $currency_input;

        if(!$currency->save()) {
            return response("Something wen't wrong.", 500);
        }

        return response('Updated!', 200);
    }

}
