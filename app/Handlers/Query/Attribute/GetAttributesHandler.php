<?php

namespace App\Handlers\Query\Attribute;

use App\Handlers\Query\QueryHandlerInterface;
use App\Models\Attribute;
use App\Queries\Attribute\GetAttributesQuery;
use Ecotone\Modelling\Attribute\QueryHandler;
use Ecotone\Modelling\QueryBus;

class GetAttributesHandler implements QueryHandlerInterface
{
    public function __construct(
        protected QueryBus $commandBus,
    ) {
    }

    #[QueryHandler]
    public function getAttributes(GetAttributesQuery $query)
    {
        if ($query->isSearchById()) {
            return Attribute::active()->findOrFail($query->getAttributeId());
        }

        return Attribute::active()->get();
    }
}
