<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Jobs\SendSubscriptionJob;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     * @throws ValidationException
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string',
            'last_name'  => 'required|string',
            'address'    => 'required|string',

            'basket' => 'required|array|min:1',
            'basket.*.name' => 'required|string',
            'basket.*.type' => 'required|string|in:unit,subscription',
            'basket.*.price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $data = $validator->validated();
        $order = Order::create([
            'first_name' => $data['first_name'],
            'last_name'  => $data['last_name'],
            'address'    => $data['address'],
        ]);

        foreach ($data['basket'] as $rawItem) {
            $item = $order->items()->create($rawItem);

            if ($item->isSubscription()) {
                SendSubscriptionJob::dispatch($item);
                Log::info('Subscription queued', ['item_id' => $item->id]);
            }
        }

        return new OrderResource($order);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
