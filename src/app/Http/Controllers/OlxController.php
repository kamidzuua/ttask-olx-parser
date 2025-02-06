<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\SubscribeRequest;
use App\Jobs\FirstTimeAdSetup;
use App\Models\Ad;
use App\Services\Olx\AdParser;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class OlxController extends Controller
{
    public function subscribe(SubscribeRequest $request): JsonResponse
    {
        $ad = Ad::updateOrCreate([
            'url' => $request->input('url'),
            'email' => $request->input('email')
        ]);

        return response()->json($ad->toArray(), Response::HTTP_CREATED);
    }
}
