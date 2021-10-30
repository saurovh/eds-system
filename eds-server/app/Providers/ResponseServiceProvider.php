<?php

namespace App\Providers;

use App\Enums\ReasonCodeValues;
use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\ResponseFactory;

class ResponseServiceProvider extends  ServiceProvider
{
    public function boot(ResponseFactory $response)
    {
        $response::macro('success', function ($data) {
            return response()->json($data);
        });

        $response::macro('error', function ($message, $reasonCode = ReasonCodeValues::BAD_REQUEST) {
            return response()->json([
                'status'  => false,
                'rc'      => $reasonCode,
                'message' => $message,
            ]);
        });
    }
}
