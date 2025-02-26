<?php

namespace App\Http\Controllers\Actions\Panel\AttributeGroups;

use App\Commands\AttributeGroup\CreateAttributeGroupCommand;
use App\Http\Controllers\Actions\Api\ApiCommandActionBase;
use App\Http\Requests\Api\AttributeGroupRequest;

class CreateAttributeGroupCommandAction extends ApiCommandActionBase
{
    public function __construct(AttributeGroupRequest $request)
    {
        parent::__construct($request);
    }

    public function handle()
    {
        $this->commandBus->send(CreateAttributeGroupCommand::createFromRequest($this->request));

        redirect();
    }
}
