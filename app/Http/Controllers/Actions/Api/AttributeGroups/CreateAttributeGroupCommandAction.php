<?php

namespace App\Http\Controllers\Actions\Api\AttributeGroups;

use App\Commands\AttributeGroup\CreateAttributeGroupCommand;
use App\Http\Controllers\Actions\Api\ApiCommandActionBase;
use App\Http\Requests\Api\AttributeGroupRequest;
use Symfony\Component\HttpFoundation\Response;
/**
 * @OA\Post(
 *     path="/api/attribute-groups",
 *     summary="[TENANT] Create a new attribute group",
 *     tags={"[TENANT] Attribute Groups"},
 *     description="Creates a new attribute group in the system.",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="name", type="string", description="The name of the attribute group", example="Color"),
 *             @OA\Property(property="description", type="string", description="A brief description of the attribute group", example="Color categories for products"),
 *         ),
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Attribute group created successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Attribute group created successfully.")
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
class CreateAttributeGroupCommandAction extends ApiCommandActionBase
{
    public function __construct(AttributeGroupRequest $request)
    {
        parent::__construct($request);
    }

    public function handle()
    {
        $this->commandBus->send(CreateAttributeGroupCommand::createFromRequest($this->request));

        response(null, Response::HTTP_CREATED)->send();
    }
}
