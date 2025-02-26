<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FirebaseRequest;
use App\Managers\FirebaseManager;
use App\Services\TranslationService;
use Illuminate\Http\Request;

class FirebaseController extends Controller
{
    /**
     * @var TranslationService
     */
    private $transService;

    /**
     * @var FirebaseManager
     */
    private $manager;

    public function __construct(TranslationService $service)
    {
        $this->transService = $service;
        $this->manager = new FirebaseManager($this->transService);
    }

    /**
     * store function.
     *
     * @param FirebaseRequest $request
     *
     * @return JsonResponse
     */
    public function store(FirebaseRequest $request)
    {
        $response = $this->manager->create($request);

        return $response;
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function delete(Request $request)
    {
        $response = $this->manager->delete($request);

        return $response;
    }
}
