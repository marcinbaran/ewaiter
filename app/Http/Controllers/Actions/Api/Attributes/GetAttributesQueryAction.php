<?php

namespace App\Http\Controllers\Actions\Api\Attributes;

use App\Http\Controllers\Actions\Api\ApiQueryActionBase;
use App\Http\Requests\Api\AttributeRequest;
use App\Http\Resources\Api\AttributeResource;
use App\Queries\Attribute\GetAttributesQuery;
/**
 * @OA\Get(
 *     path="/api/attribute",
 *     summary="[TENANT]Get all attributes",
 *     tags={"[TENANT] Attributes"},
 *     description="Retrieves a list of all attributes.",
 *     @OA\Parameter(
 *         name="page",
 *         in="query",
 *         description="Page number for pagination",
 *         required=false,
 *         @OA\Schema(type="integer", default=1)
 *     ),
 *     @OA\Parameter(
 *         name="limit",
 *         in="query",
 *         description="Number of items per page for pagination",
 *         required=false,
 *         @OA\Schema(type="integer", default=15)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="List of attributes",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(ref="#/components/schemas/AttributeResource")
 *             ),
 *             @OA\Property(
 *                 property="meta",
 *                 type="object",
 *                 @OA\Property(
 *                     property="total",
 *                     type="integer",
 *                     description="Total number of attributes"
 *                 ),
 *                 @OA\Property(
 *                     property="per_page",
 *                     type="integer",
 *                     description="Number of items per page"
 *                 ),
 *                 @OA\Property(
 *                     property="current_page",
 *                     type="integer",
 *                     description="Current page number"
 *                 ),
 *                 @OA\Property(
 *                     property="last_page",
 *                     type="integer",
 *                     description="Last page number"
 *                 ),
 *                 @OA\Property(
 *                     property="from",
 *                     type="integer",
 *                     description="First item on the current page"
 *                 ),
 *                 @OA\Property(
 *                     property="to",
 *                     type="integer",
 *                     description="Last item on the current page"
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal server error",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="error",
 *                 type="string",
 *                 example="An unexpected error occurred."
 *             )
 *         )
 *     ),
 *     security={{"bearerAuth":{}}}
 * )
 */
class GetAttributesQueryAction extends ApiQueryActionBase
{
    public function __construct(AttributeRequest $request)
    {
        parent::__construct($request);
    }

    public function handle()
    {
        $responseData = $this->queryBus->send(GetAttributesQuery::createFromRequest($this->request));

        $this->sendResourceResponse($responseData, AttributeResource::class);
    }
}
