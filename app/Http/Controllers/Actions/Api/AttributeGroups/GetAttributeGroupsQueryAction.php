<?php

namespace App\Http\Controllers\Actions\Api\AttributeGroups;

use App\Http\Controllers\Actions\Api\ApiQueryActionBase;
use App\Http\Requests\Api\AttributeGroupRequest;
use App\Http\Resources\Api\AttributeGroupResource;
use App\Queries\AttributeGroup\GetAttributeGroupsQuery;
/**
 * @OA\Get(
 *     path="/api/attribute-groups",
 *     summary="[TENANT] Get all attribute groups",
 *     tags={"[TENANT] Attribute Groups"},
 *     description="Retrieves a list of all attribute groups.",
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
 *         description="List of attribute groups",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(ref="#/components/schemas/AttributeGroupResource")
 *             ),
 *             @OA\Property(
 *                 property="meta",
 *                 type="object",
 *                 @OA\Property(
 *                     property="total",
 *                     type="integer",
 *                     description="Total number of attribute groups"
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
class GetAttributeGroupsQueryAction extends ApiQueryActionBase
{
    public function __construct(AttributeGroupRequest $request)
    {
        parent::__construct($request);
    }

    public function handle()
    {
        $responseData = $this->queryBus->send(GetAttributeGroupsQuery::createFromRequest($this->request));
        $this->sendResourceResponse($responseData, AttributeGroupResource::class);
    }
}
