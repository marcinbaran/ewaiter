<?php

namespace App\Http\Controllers\Admin;

use App\Enum\DeliveryMethod;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\EditableRequest;
use App\Http\Requests\Admin\TranslateRequest;
use App\Models\Bill;
use App\Models\FireBaseNotificationV2;
use App\Services\TranslatorService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class CommonController extends Controller
{
    public function editable(EditableRequest $request)
    {
        $modelName = $request->request->get('model', false);
        $id = $request->request->get('id', false);
        $column = $request->request->get('column', false);
        $value = $request->request->get('value', false);

        $model = app($modelName);
        if (!$model instanceof Model) {
            throw new BadRequestException('wrong_model_name');
        }

        /** @var Model $entity */
        $entity = $model->findOrFail($id);
        if (!$entity->isFillable($column)) {
            throw new BadRequestException('column_forbidden');
        }

        if ($modelName == Bill::class && $column == 'time_wait') {
            $value = Carbon::parse($entity->time_wait)->format('Y-m-d') . ' ' . $value;
        }

        if ($this->validateWithRequest($entity, $column, $value) !== true) {
            throw new BadRequestException('validation_failed');
        }

        if ($column == 'status' && $value == 2) {
            if ($entity->delivery_type == 'delivery_personal_pickup') {
                $this->sendFirebasePushNotification($entity, __('firebase.Your order is ready pickup'));
            } elseif ($entity->delivery_type == 'delivery_table') {
                $this->sendFirebasePushNotification($entity, __('firebase.Your order has been released to table'));
            } else {
                $this->sendFirebasePushNotification($entity, __('firebase.Your order is ready'));
            }
        }

        if ($column == 'status' && $value == 3) {
            $entity->released_at = Carbon::now();
            $this->sendFirebasePushNotification($entity, __('firebase.Your order has been released'));
        }

        $entity->{$column} = $value;
        $entity->save();

        return response()->noContent();
    }

    public function translateString(TranslateRequest $request, TranslatorService $translator)
    {
        return response()->json([
            'result' => $translator->translate($request->text, $request->from, $request->to),
        ]);
    }

    private function validateWithRequest(Model $entity, string $column, $value)
    {
        $request = app('App\\Http\\Requests\\Admin\\' . class_basename($entity) . 'Request');
        if (!$request instanceof FormRequest) {
            return true;
        }

        $rules = $request->rules();
        if (!isset($rules[$column])) {
            return true;
        }

        $validator = app('validator')->make([$column => $value], [$column => $rules[$column]]);

        if ($validator->fails()) {
            return false;
        }
    }

    private function sendFirebasePushNotification($entity, $message): void
    {
        FireBaseNotificationV2::create([
            'user_id' => $entity->user_id,
            'title' => __('firebase.E-waiter'),
            'body' => $message,
            'data' => json_encode([
                'title' => __('firebase.E-waiter'),
                'body' => $message,
                'url' => '/account/orders_history/' . $entity->id,
                'object_id' => $entity->id,
            ]),
        ]);
    }
}
