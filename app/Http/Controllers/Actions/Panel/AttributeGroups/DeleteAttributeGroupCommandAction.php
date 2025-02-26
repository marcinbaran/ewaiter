<?php

namespace App\Http\Controllers\Actions\Panel\AttributeGroups;

use App\Commands\AttributeGroup\DeleteAttributeGroupCommand;
use App\Http\Controllers\Actions\Api\ApiCommandActionBase;
use App\Http\Requests\Api\AttributeGroupRequest;
use Symfony\Component\HttpFoundation\Response;

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
