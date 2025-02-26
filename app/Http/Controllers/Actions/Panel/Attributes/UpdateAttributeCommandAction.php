<?php

namespace App\Http\Controllers\Actions\Panel\Attributes;

use App\Commands\Attribute\UpdateAttributeCommand;
use App\Http\Controllers\Actions\Api\ApiCommandActionBase;
use App\Http\Requests\Api\AttributeRequest;
use Symfony\Component\HttpFoundation\Response;

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
