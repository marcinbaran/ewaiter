<?php

namespace App\Http\Controllers\Actions\Api;

use Ecotone\Modelling\QueryBus;
use Illuminate\Http\Request;

abstract class ApiQueryActionBase extends ApiActionBase
{
    protected QueryBus $queryBus;

    public function __construct(Request $request)
    {
        $this->queryBus = app(QueryBus::class);

        parent::__construct($request);
    }
}
