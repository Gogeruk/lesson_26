<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Order;

class OrderController extends Controller
{
    private $method = [
        'method_update'               => '/getUpdates',
        'method_update_parameter'     => '?offset=',
        'method_update_parameter_val' => -1,

        'method_send'               => '/sendMessage',
        'method_send_parameter'     => '?',
        'method_send_parameter_val' => null,
    ];


    public function botOrder()
    {
        $old_message = null;
        
        while (true) {
            $response = Http::get('https://api.telegram.org/bot'
                .env('TELEGRAM_API_kEY')
                .$this->method['method_update']
                .$this->method['method_update_parameter']
                .$this->method['method_update_parameter_val']);

            $updated_result = json_decode($response->body(), 'assoc');

            if ($updated_result['result'][0]['update_id'] == $old_message) {
                continue;
            }

            foreach ($updated_result['result'] as $key => $updated_message) {
                $old_message = $updated_message['update_id'];

                if (strpos($updated_message['message']['text'], "/order") !== false ) {
                    preg_match_all('!\d+!', $updated_message['message']['text'], $id);

                    $order = Order::find($id[0][0] ?? null);
                    if ($order == null) {
                        $text = 'wrong order id';
                    }else {
                        $status = 'on the way!';
                        if ($order->status == 1) {
                            $status = 'arrived!';
                        }
                        $text = 'order: '.$order->title.' status: '.$status;
                    }

                    $send_message = Http::get('https://api.telegram.org/bot'
                        .env('TELEGRAM_API_kEY')
                        .$this->method['method_send']
                        .$this->method['method_send_parameter'], [
                            'chat_id' => $updated_message['message']['chat']['id'],
                            'text'    => $text,
                        ]);
                }
                break;
            }
        }
    }
}
