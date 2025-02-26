<?php

namespace App\Http\Controllers\Actions\Panel\AttributeGroups;

use App\Http\Controllers\Actions\Panel\PanelQueryActionBase;
use App\Http\Requests\Api\AttributeGroupRequest;
use App\Http\Resources\Admin\AdditionResource;
use App\Models\Addition;
use App\Queries\AttributeGroup\GetAttributeGroupsQuery;

class GetAttributeGroupsQueryAction extends PanelQueryActionBase
{
    public function __construct(AttributeGroupRequest $request)
    {
        parent::__construct($request);
    }

    public function handle()
    {
        $responseData = $this->queryBus->send(GetAttributeGroupsQuery::createFromRequest($this->request));

        $this->sendViewResponse(view('admin.additions.form')->with($this->hydrateData([
            'data' => new AdditionResource(new Addition),
            'oldAdditionGroups' => $this->getOldArrayForSelect2('addition_addition_group', 'id'),
            'defaultRedirectUrl' => route('admin.additions.index'),
        ], $this->request)));
    }
}
