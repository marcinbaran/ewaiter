<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\WorktimeRequest;
use App\Managers\WorktimeManager;
use App\Models\Worktime;
use App\Services\TranslationService;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="[TENANT] Worktimes",
 *     description="[TENANT] API Endpoints for managing worktimes"
 * )
 */
/**
 * Api for worktimes resource.
 */
class WorktimeController extends ApiController
{
    /**
     * @var TranslationService
     */
    private $transService;

    /**
     * @var WorktimeManager
     */
    private $manager;

    public function __construct(TranslationService $service)
    {
        parent::__construct();
        $this->transService = $service;
        $this->manager = new WorktimeManager($this->transService);
    }
    /**
     * @OA\Get(
     *     path="/api/worktimes",
     *     operationId="getAllWorktimes",
     *     tags={"[TENANT] Worktimes"},
     *     summary="[TENANT] Get collection of worktimes",
     *     description="Retrieve a list of worktimes based on the specified date.",
     *     @OA\Parameter(
     *         name="date",
     *         in="query",
     *         description="Filter by specific date(s)",
     *         required=false,
     *         @OA\Schema(type="array", @OA\Items(type="string", format="date")),
     *         example={"2024-07-30"}
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="A list of worktimes",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Worktime")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     security={{"bearerAuth":{}}}
     * )
     */

    public function index(Request $request): \Illuminate\Support\Collection
    {
        $date = (array) $request->date;

        return Worktime::getRows($date);
    }
}
