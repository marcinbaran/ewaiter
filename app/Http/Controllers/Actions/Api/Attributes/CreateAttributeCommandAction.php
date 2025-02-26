<?php

namespace App\Http\Controllers\Actions\Api\Attributes;

use App\Commands\Attribute\CreateAttributeCommand;
use App\Http\Controllers\Actions\Api\ApiCommandActionBase;
use App\Http\Requests\Api\AttributeRequest;
use Symfony\Component\HttpFoundation\Response;

/**
 * @OA\Post(
 *     path="/api/attribute",
 *     summary="[TENANT] Create a new attribute",
 *     tags={"[TENANT] Attributes"},
 *     description="Creates a new attribute and returns a response indicating success.",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="name", type="string", description="The name of the attribute", example="Color"),
 *             @OA\Property(property="type", type="string", description="The type of the attribute", example="String"),
 *             @OA\Property(property="description", type="string", description="A brief description of the attribute", example="The color of the product")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Successfully created the attribute",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Attribute created successfully.")
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
class CreateAttributeCommandAction extends ApiCommandActionBase
{
    public function __construct(AttributeRequest $request)
    {
        parent::__construct($request);
    }

    public function handle()
    {
        $this->commandBus->send(CreateAttributeCommand::createFromRequest($this->request));

        response(null, Response::HTTP_CREATED)->send();
    }
}
