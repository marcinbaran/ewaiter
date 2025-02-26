<?php

namespace App\Managers;

use App\Enum\Table\TableCreateFormType;
use App\Http\Controllers\ParametersTrait;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RoomManager
{
    use ParametersTrait;

    private const NUMBER = 'number';

    /**
     * @param Request $request
     *
     * @return Room
     */
    public function create(Request $request): Room
    {
        if ($request->get('adding_type') == TableCreateFormType::RANGE->value) {
            $from_number = $request->get('from_number');
            $to_number = $request->get('to_number');

            $params = $this->getParams($request, ['floor', 'redirect']);

            for ($i = $from_number; $i <= $to_number; $i++) {
                $params['number'] = $i;
                $params['name'] = $i;
                $room = Room::create($params)->fresh();
            }

            return $room;
        } else {
            $params = $this->getParams($request, ['name', 'floor', 'number', 'redirect']);
            $params['number'] = Str::slug($params['number']);

            return  Room::create($params)->fresh();
        }
    }

    /**
     * @param Request $request
     * @param Room   $room
     * @param bool    $isAdmin
     *
     * @return Room
     */
    public function update(Request $request, Room $room): Room
    {
        $params = $this->getParams($request, ['name', 'floor', 'number', 'redirect']);
        $params['number'] = Str::slug($params['number']);

        if (! empty($params)) {
            $room->update($params);
            $room->fresh();
        }

        return $room;
    }

    /**
     * @param Room $room
     *
     * @return Room
     */
    public function delete(Room $room): Room
    {
        $room->delete();

        return $room;
    }

    public function isTableNumberChanged(Request $request, Room $room): bool
    {
        return $request->get(self::NUMBER) !== $room->number;
    }
}
