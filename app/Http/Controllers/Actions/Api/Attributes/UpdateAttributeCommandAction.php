<?php

namespace App\Http\Controllers\Actions\Api\Attributes;

use App\Commands\Attribute\UpdateAttributeCommand;
use App\Http\Controllers\Actions\Api\ApiCommandActionBase;
use App\Http\Requests\Api\AttributeRequest;
use Symfony\Component\HttpFoundation\Response;
/**
 * @OA\Put(
 *     path="/api/attribute/{id}",
 *     summary="[TENANT] Update an attribute",
 *     tags={"[TENANT] Attributes"},
 *     description="Updates the details of an existing attribute.",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Unique identifier of the attribute to be updated",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="key",
 *                 type="string",
 *                 description="Key identifier for the attribute",
 *                 example="color"
 *             ),
 *             @OA\Property(
 *                 property="name",
 *                 type="string",
 *                 description="Translated name of the attribute",
 *                 example="Color"
 *             ),
 *             @OA\Property(
 *                 property="description",
 *                 type="string",
 *                 description="Translated description of the attribute",
 *                 example="The color of the item"
 *             ),
 *             @OA\Property(
 *                 property="icon",
 *                 type="string",
 *                 nullable=true,
 *                 description="Icon associated with the attribute",
 *                 example="color-icon.png"
 *             ),
 *             @OA\Property(
 *                 property="attribute_group_id",
 *                 type="integer",
 *                 description="Identifier of the group this attribute belongs to",
 *                 example=1
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Attribute updated successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="Attribute updated successfully."
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid input parameters",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="error",
 *                 type="string",
 *                 example="Invalid input provided."
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Attribute not found",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="error",
 *                 type="string",
 *                 example="Attribute not found."
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
class UpdateAttributeCommandAction extends ApiCommandActionBase
{
    public function __construct(AttributeRequest $request)
    {
        parent::__construct($request);
    }

    public function handle()
    {
        $this->commandBus->send(UpdateAttributeCommand::createFromRequest($this->request));

        response(null, Response::HTTP_OK)->send();
    }
}
