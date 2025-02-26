<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\RequestTrait;
use App\Rules\Friend;
use Illuminate\Foundation\Http\FormRequest;
/**
 * @OA\Schema(
 *     schema="FriendRequestGET",
 *     type="object",
 *     @OA\Property(
 *         property="status",
 *         type="integer",
 *         description="Status of the friend request (0 - pending, 1 - accepted, 2 - declined, etc.)"
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="FriendRequestPOST",
 *     type="object",
 *     @OA\Property(
 *         property="receiver_id",
 *         type="integer",
 *         description="ID of the receiver user"
 *     ),
 *     @OA\Property(
 *         property="phone",
 *         type="string",
 *         description="Phone number of the receiver"
 *     ),
 *     @OA\Property(
 *         property="email",
 *         type="string",
 *         description="Email of the receiver"
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="FriendRequestPUT",
 *     type="object",
 *     @OA\Property(
 *         property="status",
 *         type="integer",
 *         description="Status of the friend request"
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="FriendRequestDELETE",
 *     type="object"
 * )
 */
class FriendRequest extends FormRequest
{
    use RequestTrait;

    /**
     * @var array
     */
    private static $rules = [
        self::METHOD_GET => [
            'status' => 'between:0,4',
        ],
        self::METHOD_POST => [
            'receiver_id' => 'integer|min:1|exists:users,id',
            'phone' => 'size:9|exists:users,phone',
            'email' => 'email|exists:users,email',
        ],
        self::METHOD_PUT => [
        ],
        self::METHOD_DELETE => [
        ],
    ];

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            self::METHOD_GET => [
                'status' => 'between:0,1',
            ],
            self::METHOD_POST => [
                'receiverId' => new Friend\ReceiverIdRule,
                'phone' => new Friend\PhoneRule,
                'email' => new Friend\EmailRule,
            ],
            self::METHOD_PUT => [
                'status' => 'integer|between:0,1',
            ],
            self::METHOD_DELETE => [
            ],
        ];

        return $rules[$this->getMethod()] ?? [];
    }
}
