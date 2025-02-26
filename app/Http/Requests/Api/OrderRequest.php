<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\RequestTrait;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
/**
 * @OA\Schema(
 *     schema="OrderRequest_GET",
 *     type="object",
 *     @OA\Property(property="itemsPerPage", type="integer", example=20, description="Number of items per page"),
 *     @OA\Property(property="page", type="integer", example=1, description="Page number"),
 *     @OA\Property(property="id", type="array", @OA\Items(type="integer"), description="Array of order IDs"),
 *     @OA\Property(property="table", type="array", @OA\Items(type="integer"), description="Array of table IDs"),
 *     @OA\Property(property="bill", type="array", @OA\Items(type="integer"), description="Array of bill IDs"),
 *     @OA\Property(property="dish", type="array", @OA\Items(type="integer"), description="Array of dish IDs"),
 *     @OA\Property(property="status", type="array", @OA\Items(type="integer"), description="Array of statuses"),
 *     @OA\Property(property="order.id", type="string", enum={"asc", "desc"}, description="Order by ID"),
 *     @OA\Property(property="order.createdAt", type="string", enum={"asc", "desc"}, description="Order by creation date"),
 *     @OA\Property(property="order.updatedAt", type="string", enum={"asc", "desc"}, description="Order by update date"),
 *     @OA\Property(property="order.price", type="string", enum={"asc", "desc"}, description="Order by price"),
 *     @OA\Property(property="order.status", type="string", enum={"asc", "desc"}, description="Order by status"),
 *     @OA\Property(property="order.quantity", type="string", enum={"asc", "desc"}, description="Order by quantity"),
 *     @OA\Property(property="order.discount", type="string", enum={"asc", "desc"}, description="Order by discount"),
 *     @OA\Property(property="paid", type="boolean", description="Filter by paid status"),
 * )
 *
 * @OA\Schema(
 *     schema="OrderRequest_POST",
 *     type="object",
 *     required={"quantity", "dish.id", "bill.id"},
 *     @OA\Property(property="quantity", type="integer", example=1, description="Quantity of the dish"),
 *     @OA\Property(property="dish.id", type="integer", example=1, description="Dish ID"),
 *     @OA\Property(property="bill.id", type="integer", example=1, description="Bill ID"),
 *     @OA\Property(property="price", type="number", format="float", example=10.50, description="Price of the order"),
 *     @OA\Property(property="table.id", type="integer", example=1, description="Table ID"),
 *     @OA\Property(property="tax", type="number", format="float", example=1.23, description="Tax amount"),
 *     @OA\Property(property="discount", type="number", format="float", example=0.50, description="Discount amount"),
 *     @OA\Property(
 *         property="additions",
 *         type="array",
 *         @OA\Items(
 *             type="object",
 *             @OA\Property(property="id", type="integer", example=1, description="Addition ID"),
 *             @OA\Property(property="price", type="number", format="float", example=1.50, description="Addition price"),
 *             @OA\Property(property="groupName", type="string", example="Extras", description="Group name of the addition")
 *         ),
 *         description="Array of additions"
 *     ),
 *     @OA\Property(property="roast", type="integer", example=3, description="Roast level"),
 * )
 *
 * @OA\Schema(
 *     schema="OrderRequest_PUT",
 *     type="object",
 *     @OA\Property(property="quantity", type="integer", example=1, description="Quantity of the dish"),
 *     @OA\Property(property="dish.id", type="integer", example=1, description="Dish ID"),
 *     @OA\Property(property="bill.id", type="integer", example=1, description="Bill ID"),
 *     @OA\Property(property="price", type="number", format="float", example=10.50, description="Price of the order"),
 *     @OA\Property(property="status", type="integer", example=2, description="Status of the order"),
 *     @OA\Property(property="paid", type="boolean", description="Paid status"),
 *     @OA\Property(property="table.id", type="integer", example=1, description="Table ID"),
 *     @OA\Property(property="tax", type="number", format="float", example=1.23, description="Tax amount"),
 *     @OA\Property(property="discount", type="number", format="float", example=0.50, description="Discount amount"),
 *     @OA\Property(
 *         property="additions",
 *         type="array",
 *         @OA\Items(
 *             type="object",
 *             @OA\Property(property="id", type="integer", example=1, description="Addition ID"),
 *             @OA\Property(property="price", type="number", format="float", example=1.50, description="Addition price"),
 *             @OA\Property(property="groupName", type="string", example="Extras", description="Group name of the addition")
 *         ),
 *         description="Array of additions"
 *     ),
 *     @OA\Property(property="roast", type="integer", example=3, description="Roast level"),
 * )
 *
 * @OA\Schema(
 *     schema="OrderRequest_DELETE",
 *     type="object",
 * )
 */

class OrderRequest extends FormRequest
{
    use RequestTrait;

    /**
     * @var array
     */
    private static $rules = [
        self::METHOD_GET => [
            'itemsPerPage' => 'integer|min:1|max:50',
            'page' => 'integer|min:1',
            'id' => 'array|min:1',
            'id.*' => 'integer|min:1',
            'table' => 'array|min:1',
            'table.*' => 'integer|min:1',
            'bill' => 'array|min:1',
            'bill.*' => 'integer|min:1',
            'dish' => 'array|min:1',
            'dish.*' => 'integer|min:1',
            'status' => 'array|min:1',
            'status.*' => 'integer|min:0|max:4',
            'order.id' => 'string|in:asc,desc',
            'order.createdAt' => 'string|in:asc,desc',
            'order.updatedAt' => 'string|in:asc,desc',
            'order.price' => 'string|in:asc,desc',
            'order.status' => 'string|in:asc,desc',
            'order.quantity' => 'string|in:asc,desc',
            'order.discount' => 'string|in:asc,desc',
            'paid' => 'boolean',
        ],
        self::METHOD_POST => [
            'quantity' => 'required|integer|min:1',
            'dish.id' => 'required|integer|min:1|exists:tenant.dishes,id',
            'bill.id' => 'required|integer|min:1|exists:tenant.bills,id',
            'price' => 'nullable|numeric|min:0',
            'table.id' => 'integer|min:1|exists:tenant.tables,id',
            'tax' => 'numeric|min:0',
            'discount' => 'numeric|min:0',
            'additions' => 'array',
            'additions.*.id' => 'integer|min:1|exists:tenant.additions,id',
            'additions.*.price' => 'numeric|min:0',
            'additions.*.groupName' => 'nullable|string|max:100',
            'additions.*.dish_id' => 'integer|min:1|exists:tenant.dishes,id',
            'roast' => 'int|min:1|max:6',
        ],
        self::METHOD_PUT => [
            'quantity' => 'integer|min:0',
            'dish.id' => 'integer|min:1|exists:tenant.dishes,id',
            'bill.id' => 'integer|min:1|exists:tenant.bills,id',
            'price' => 'numeric|min:0',
            'status' => 'integer|min:0|max:4',
            'paid' => 'boolean',
            'table.id' => 'integer|min:1|exists:tenant.tables,id',
            'tax' => 'numeric|min:0',
            'discount' => 'numeric|min:0',
            'additions' => 'array',
            'additions.*.id' => 'integer|min:1|exists:tenant.additions,id',
            'additions.*.price' => 'numeric|min:0',
            'additions.*.groupName' => 'nullable|string|max:100',
            'additions.*.dish_id' => 'integer|min:1|exists:tenant.dishes,id',
            'roast' => 'int|min:1|max:6',
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
        $user = Auth::user();
        $rules = self::$rules[$this->getMethod()] ?? [];

        if (self::METHOD_POST === $this->getMethod() && ! $user->table) {
            $rules['table.id'] .= '|required';
        }

        if (in_array($this->getMethod(), [self::METHOD_POST, self::METHOD_PUT]) && ! $user->isOne([User::ROLE_MANAGER, User::ROLE_ADMIN])) {
            unset($rules['paid']);
        }

        return $rules;
    }
}
