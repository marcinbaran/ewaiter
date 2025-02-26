<?php

namespace App\Http\Controllers\Actions\Panel\Attributes;

use App\Commands\Attribute\CreateAttributeCommand;
use App\Http\Controllers\Actions\Api\ApiCommandActionBase;
use App\Http\Requests\Api\AttributeRequest;
use Symfony\Component\HttpFoundation\Response;

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
