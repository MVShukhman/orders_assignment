<?php

namespace App\Jobs;

use App\Models\OrderItem;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendSubscriptionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    protected OrderItem $item;

    public function __construct(OrderItem $item)
    {
        $this->item = $item;
    }

    public function handle()
    {
        try {
            $response = Http::post(config('services.slowapi.url'), [
                'ProductName'      => $this->item->name,
                'Price'     => $this->item->price,
                'Timestamp'   => now()->toDateTimeString(),
            ]);

            Log::info('Subscription item sent to slowapi', [
                'status'  => $response->status(),
                'body'    => $response->json(),
                'item_id' => $this->item->id,
            ]);
        } catch (\Exception $e) {
            Log::error('Error calling slowapi for subscription item', [
                'error'   => $e->getMessage(),
                'item_id' => $this->item->id,
            ]);
        }
    }
}
