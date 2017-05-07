<?php

namespace App\Http\Controllers\Telegram;

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

    public function index() {
        $return_data = file_get_contents('php://input');

        $result = json_decode($return_data, true);

        $chat_id = -1;
        $message = '';

        if(array_key_exists('message', $result)) {
            $chat_id = $result['message']['chat']['id'];
            $message = $result['message']['text'];
        }

        if(array_key_exists('edited_message', $result)) {
            $chat_id = $result['edited_message']['chat']['id'];
            $message = $result['edited_message']['text'];
        }

        $this->chat_id = $chat_id;

        switch(true) {
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
            default:
                $this->sendMessage('Hi!');
                break;
        }

        private function calculateBTC($exploded = []) {

        }

        private function get_btc_data($exploded = []) {

        }

        private function sendMessage($message) {
            $url = $this->base_url.'/sendMessage?chat_id=' . $this->chat_id . '&text='.urlencode($message) . '';
            file_get_contents($url);
        }
    }

}
