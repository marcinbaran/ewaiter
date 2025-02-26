<?php

namespace App\Managers;

use App\Http\Controllers\ParametersTrait;
use App\Models\Firebase;
use App\Services\TranslationService;
use Illuminate\Http\Request;

class FirebaseManager
{
    use ParametersTrait;

    /**
     * @var TranslationService
     */
    private $transService;

    /**
     * @param TranslationService $service
     */
    public function __construct(TranslationService $service)
    {
        $this->transService = $service;
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function create(Request $request)
    {
        $token = $request->input('token');
        $user_id = auth()->user()->id;

        if (! $token) {
            $response = [
                    'status' => 'error',
                    'code' => 400,
                    'komunikat' => 'Błędny token PUSH!',
                ];

            return response()->json($response);
        }

        $firebase = Firebase::where('user_id', $user_id)->where('token', $token)->first();
        if ($firebase) {
            $response = [
                    'status' => 'success',
                    'code' => 400,
                ];

            return response()->json($response);
        }
        $firebase = new Firebase();
        $firebase->token = $token;
        $firebase->user_id = $user_id;
        $firebase->save();

        $response = [
                'status' => 'success',
                'code' => 200,
            ];

        return response()->json($response);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function delete(Request $request)
    {
        $token = $request->input('token');

        if (! $token) {
            $response = [
                    'status' => 'error',
                    'code' => 400,
                    'komunikat' => 'Błędny token PUSH!',
                ];

            return response()->json($response);
        }

        $firebase = Firebase::where('token', $token)->first();
        if (! $firebase) {
            $response = [
                    'status' => 'success',
                    'code' => 400,
                ];

            return response()->json($response);
        }
        $firebase->delete();

        $response = [
                'status' => 'success',
                'code' => 200,
            ];

        return response()->json($response);
    }
}
