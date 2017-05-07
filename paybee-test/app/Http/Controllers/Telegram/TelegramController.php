<?php

namespace App\Http\Controllers\Telegram;

use App\BotSetting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class TelegramController extends Controller {

    protected $base_token;
    protected $base_url;
    protected $chat_id;

    public function __construct() {
        $this->base_token = env('TELEGRAM_BOT_TOKEN');
        $this->base_url = 'https://api.telegram.org/bot'. $this->base_token;
    }

    public function index()
    {
        $return_data = file_get_contents('php://input');

        $result = json_decode($return_data, true);

        $chat_id = -1;
        $message = '';

        Log::debug($result);

        if (array_key_exists('message', $result)) {
            $chat_id = $result['message']['chat']['id'];
            $message = $result['message']['text'];
        }

        if (array_key_exists('edited_message', $result)) {
            $chat_id = $result['edited_message']['chat']['id'];
            $message = $result['edited_message']['text'];
        }

        $this->chat_id = $chat_id;

        Log::debug('message ' . $message);

        switch (true) {
            case stristr($message, "/btcequivalent"):

                $message = str_replace('/btcequivalent ', '', $message);

                $exploded = explode(' ', $message);

                if(count($exploded) == 0) {
                    $this->sendMessage('Please provide an amount and currency you wish to convert.');
                    return false;
                }

                $btc_data = $this->calculateBTC($exploded);

                if($btc_data) {
                    $btc_quantity = $btc_data['btc_quantity'];
                    $btc_rate = $btc_data['rate'];

                    $message = $btc_data['value'] . ' ' . $btc_data['currency'] . ' = ' . $btc_quantity . ' BTC. (1 BTC = ' . $btc_rate . ' ' . $btc_data['currency'] . ')';

                    $this->sendMessage($message);
                }

                break;
            case stristr($message, "/getuserid"):
                $getMe = file_get_contents($this->base_url .'/getMe');
                $data = json_decode($getMe);

                $message = str_replace('/getuserid', 'Your user ID is: ' . $data->result->id, $message);

                $this->sendMessage($message);
                break;
            default:
                $this->sendMessage('Hi! To get started, try typing /btcequivalent 100 USD to get the conversion of 100 USD to BTC.');
                break;
        }
    }

    private function calculateBTC($exploded = []) {
        $data = $this->get_btc_data($exploded);

        if(!$data) {
            return false;
        }

        $currency = strtoupper($data['currency']);
        $value = $data['value'];

        $data = json_decode(json_encode($data['data']), true);
        $data = json_decode($data);

        $bpi = json_decode(json_encode($data->bpi), true);

        $rate = $bpi[$currency]['rate_float'];
        $btc_qty = ($value / $rate);

        return ['btc_quantity' => $btc_qty, 'rate' => $rate, 'currency' => $currency, 'value' => $value];
    }

    private function get_btc_data($exploded = []) {

        $default_currency = BotSetting::where('key', 'default.currency')->first();
        if(!$default_currency) {
            $default_currency = 'USD';
        } else {
            $default_currency = $default_currency->value;
        }

        $param_1 = 1;
        $param_2 = $default_currency;

        $value = 1;
        $currency = $default_currency;

        if(count($exploded) > 0) {
            $param_1 = $exploded[0];
        }

        if(count($exploded) > 1) {
            $param_2 = $exploded[1];
        }

        try {
            if(is_numeric($param_1)) {
                $value = $param_1;
                $currency = $param_2;
            }
            if(!is_numeric($param_1)) {
                $value = $param_2;
                $currency = $param_1;
            }

            if(!$currency || $currency == '')
                return false;

            $endpoint = 'http://api.coindesk.com/v1/bpi/currentprice/' . strtoupper($currency) . '.json';
            $data = file_get_contents($endpoint);
        } catch (\Exception $e) {
            $this->sendMessage('Sorry, that currency is not valid.');
            Log::error($e);
            return false;
        }

        return ['currency' => $currency, 'value' => $value, 'data' => $data];
    }

    private function sendMessage($message) {
        $url = $this->base_url.'/sendMessage?chat_id=' . $this->chat_id . '&text='.urlencode($message) . '';
        file_get_contents($url);
    }

}
