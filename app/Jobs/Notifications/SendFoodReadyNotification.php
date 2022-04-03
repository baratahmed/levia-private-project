<?php

namespace App\Jobs\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendFoodReadyNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

        
    /**
     * Order Object
     *
     * @var Order
     */
    private $order = null;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $payload = [
            'id'=>$this->order->id,
            'type'=>'new_order',
            'title' => 'Order (#'.$this->order->order_number.') is ready for pickup',
            'sub_title' => '',
            'message' => 'Order (#'.$this->order->order_number.') is ready for pickup',
        ];

        // Broadcast Notification to all Foodman Firebase Cloud Message

        sendNotification(null, $payload, "broadcast", "Levia_DR");
    }
}
