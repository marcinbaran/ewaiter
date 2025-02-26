<?php

namespace App\Http\Controllers\Actions\Api\AttributeGroups;

use App\Commands\AttributeGroup\UpdateAttributeGroupCommand;
use App\Http\Controllers\Actions\Api\ApiCommandActionBase;
use App\Http\Requests\Api\AttributeGroupRequest;
use Symfony\Component\HttpFoundation\Response;
/**
 * @OA\Put(
 *     path="/api/attribute-groups/{id}",
 *     summary="[TENANT] Update an attribute group",
 *     tags={"[TENANT] Attribute Groups"},
 *     description="Updates the details of an existing attribute group.",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="The unique identifier of the attribute group to update",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="name", type="string", description="The name of the attribute group", example="Updated Attribute Group"),
 *             @OA\Property(property="description", type="string", description="A brief description of the attribute group", example="Updated description")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successfully updated the attribute group",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Attribute group updated successfully.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Bad request due to validation error",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="error", type="string", example="Validation error occurred.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Attribute group not found",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="error", type="string", example="Attribute group not found.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Internal server error",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="error", type="string", example="An unexpected error occurred.")
 *         )
 *     ),
 *     security={{"bearerAuth":{}}}
 * )
 */
class UpdateAttributeGroupCommandAction extends ApiCommandActionBase
{
    public function __construct(AttributeGroupRequest $request)
    {
        parent::__construct($request);
    }

    public function handle()
    {
        $this->commandBus->send(UpdateAttributeGroupCommand::createFromRequest($this->request));

        response(null, Response::HTTP_OK)->send();
    }
}
