<?php

namespace App\Http\Controllers\Actions\Panel\Attributes;

use App\Http\Controllers\Actions\Api\ApiQueryActionBase;
use App\Http\Requests\Api\AttributeRequest;
use App\Http\Resources\Api\AttributeResource;
use App\Queries\Attribute\GetAttributesQuery;

class GetAttributesQueryAction extends ApiQueryActionBase
{
    public function __construct(AttributeRequest $request)
    {
        parent::__construct($request);
    }

    public function handle()
    {
        $responseData = $this->queryBus->send(GetAttributesQuery::createFromRequest($this->request));

        $this->sendResourceResponse($responseData, AttributeResource::class);
    }
}
