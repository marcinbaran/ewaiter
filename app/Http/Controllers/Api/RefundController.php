<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\RefundRequest;
use App\Http\Resources\Api\RefundResource;
use App\Managers\RefundManager;
use App\Models\Refund;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * @OA\Tag(
 *     name="[TENANT] Refunds",
 *     description="[TENANT] API Endpoints for managing Refunds"
 * )
 */
class RefundController extends ApiController
{
    /**
     * @var RefundManager
     */
    private $manager;

    public function __construct()
    {
        parent::__construct();
        $this->manager = new RefundManager();
    }
    /**
     * @OA\Get(
     *     path="/api/refunds",
     *     summary="[TENANT] Get a list of refunds",
     *     tags={"[TENANT] Refunds"},
     *     @OA\Parameter(
     *         name="itemsPerPage",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=20)
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         description="Filter by refund ID(s). Can be array or single value.",
     *         required=false,
     *         @OA\Schema(type="array", @OA\Items(type="integer"))
     *     ),
     *     @OA\Parameter(
     *         name="bill",
     *         in="query",
     *         description="Filter by bill ID(s). Can be array or single value.",
     *         required=false,
     *         @OA\Schema(type="array", @OA\Items(type="integer"))
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of refunds",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/RefundResource")),
     *             @OA\Property(property="locale", type="string", example="en")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */

    public function index(RefundRequest $request): AnonymousResourceCollection
    {
        $limit = $request->query->get('itemsPerPage', RefundResource::LIMIT);
        $offset = ($request->query->get('page', 1) - 1) * $limit;
        $order = $request->input('order', ['id' => 'asc']);
        $criteria = ['id' => (array) $request->id, 'bill' => (array) $request->bill];

        return RefundResource::collection(Refund::getRows($criteria, $order, $limit, $offset));
    }
}
