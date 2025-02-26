<?php

namespace App\Http\Controllers\Actions\Api\Attributes;

use App\Commands\Attribute\DeleteAttributeCommand;
use App\Http\Controllers\Actions\Api\ApiCommandActionBase;
use App\Http\Requests\Api\AttributeRequest;
use Symfony\Component\HttpFoundation\Response;
/**
 * @OA\Delete(
 *     path="/api/attribute/{id}",
 *     summary="[TENANT]Delete an attribute",
 *     tags={"[TENANT] Attributes"},
 *     description="Deletes an attribute based on the provided ID.",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="The ID of the attribute to delete",
 *         @OA\Schema(
 *             type="integer",
 *             example=1
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successfully deleted the attribute",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Attribute deleted successfully.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Bad request due to validation error or invalid ID",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="error", type="string", example="Invalid ID provided.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Attribute not found",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="error", type="string", example="Attribute not found.")
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
class DeleteAttributeCommandAction extends ApiCommandActionBase
{
    public function __construct(AttributeRequest $request)
    {
        parent::__construct($request);
    }

    public function handle()
    {
        $this->commandBus->send(DeleteAttributeCommand::createFromRequest($this->request));

        response(null, Response::HTTP_OK)->send();
    }
}
