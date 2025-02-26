<?php

namespace App\Http\Controllers\Actions\Api\AttributeGroups;

use App\Commands\AttributeGroup\DeleteAttributeGroupCommand;
use App\Http\Controllers\Actions\Api\ApiCommandActionBase;
use App\Http\Requests\Api\AttributeGroupRequest;
use Symfony\Component\HttpFoundation\Response;
/**
 * @OA\Delete(
 *     path="/api/attribute-groups/{id}",
 *     summary="[TENANT] Delete an attribute group",
 *     tags={"[TENANT] Attribute Groups"},
 *     description="Deletes an existing attribute group by its ID.",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="The ID of the attribute group to delete.",
 *         @OA\Schema(type="integer"),
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Attribute group deleted successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Attribute group deleted successfully.")
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
class DeleteAttributeGroupCommandAction extends ApiCommandActionBase
{
    public function __construct(AttributeGroupRequest $request)
    {
        parent::__construct($request);
    }

    public function handle()
    {
        $this->commandBus->send(DeleteAttributeGroupCommand::createFromRequest($this->request));

        response(null, Response::HTTP_OK)->send();
    }
}
