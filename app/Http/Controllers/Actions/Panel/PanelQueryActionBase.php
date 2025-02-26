<?php

namespace App\Http\Controllers\Actions\Panel;

use Ecotone\Modelling\QueryBus;
use Illuminate\Http\Request;

abstract class PanelQueryActionBase extends PanelActionBase
{
    protected QueryBus $queryBus;

    public function __construct(Request $request)
    {
        $this->queryBus = app(QueryBus::class);

        parent::__construct($request);
    }
}
