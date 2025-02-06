<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\SubscribeRequest;
use App\Jobs\FirstTimeAdSetup;
use App\Models\Ad;
use App\Models\Email;
use App\Services\Olx\AdParser;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class OlxController extends Controller
{
    public function subscribe(SubscribeRequest $request): JsonResponse
    {
        /**
         * @var $ad Ad
         */
        $ad = Ad::updateOrCreate([
            'url' => $request->input('url'),
        ]);

        /**
         * @var $email Email
         */
        $email = Email::updateOrCreate([
            'email' => $request->input('email')
        ]);

        $ad->emails()->attach($email);

        return response()->json(['ad' => $ad->toArray(), 'email' => $email->toArray()], Response::HTTP_CREATED);
    }
}
