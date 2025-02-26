<?php

namespace App\Handlers\Query\AttributeGroup;

use App\Handlers\Query\QueryHandlerInterface;
use App\Models\AttributeGroup;
use App\Queries\AttributeGroup\GetAttributeGroupsQuery;
use Ecotone\Modelling\Attribute\QueryHandler;
use Ecotone\Modelling\QueryBus;

class GetAttributeGroupsHandler implements QueryHandlerInterface
{
    public function __construct(
        protected QueryBus $commandBus,
    ) {
    }

    #[QueryHandler]
    public function getAttributes(GetAttributeGroupsQuery $query)
    {
        if ($query->isSearchById()) {
            return AttributeGroup::active()->findOrFail($query->getAttributeGroupId());
        }

        return AttributeGroup::active()->get();
    }
}
