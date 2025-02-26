<?php

namespace App\Managers;

use App\Enum\Table\TableCreateFormType;
use App\Http\Controllers\ParametersTrait;
use App\Models\Table;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class TableManager
{
    use ParametersTrait;

    /**
     * @param Request $request
     *
     * @return Table
     */
    public function create(Request $request): Table
    {
        $references = $this->getParams($request, ['user']);
        $user = Auth::user();
        $userId = $references['user']['id'] ?? ($user->hasRoles([User::ROLE_TABLE, User::ROLE_USER]) ? $user->id : null);

        if ($request->get('adding_type') == TableCreateFormType::RANGE->value) {
            $from_number = $request->get('from_number');
            $to_number = $request->get('to_number');

            $params = $this->getParams($request, ['description', 'people_number', 'active', 'redirect']);
            $params['user_id'] = $userId;

            for ($i = $from_number; $i <= $to_number; $i++) {
                $params['number'] = $i;
                $params['name'] = $i;
                $table = Table::create($params)->fresh();
            }

            return $table;
        } else {
            $params = $this->getParams($request, ['name', 'number', 'description', 'people_number', 'active', 'redirect']);
            $params['user_id'] = $userId;
            $params['number'] = Str::slug($params['number']);

            return Table::create($params)->fresh();
        }
    }

    /**
     * @param Request $request
     * @param Table $table
     * @param bool $isAdmin
     *
     * @return Table
     */
    public function update(Request $request, Table $table): Table
    {
        $params = $this->getParams($request, ['name', 'number', 'description', 'people_number', 'active', 'redirect']);
        $references = $this->getParams($request, ['user']);

        ! isset($references['user']['id']) ?: $params['user_id'] = $references['user']['id'];

        $params['number'] = Str::slug($params['number']);

        if (! empty($params)) {
            $table->update($params);
            $table->fresh();
        }

        return $table;
    }

    /**
     * @param Table $table
     *
     * @return Table
     */
    public function delete(Table $table): Table
    {
        $table->delete();

        return $table;
    }

    public function isTableNumberChanged(Request $request, Table $table): bool
    {
        return $request->get('number') !== $table->number;
    }
}
