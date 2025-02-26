<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\RequestTrait;
use Illuminate\Foundation\Http\FormRequest;
/**
 * @OA\Schema(
 *     schema="TagRequestGET",
 *     type="object",
 *     @OA\Property(
 *         property="itemsPerPage",
 *         type="integer",
 *         example=10,
 *         description="Liczba elementów na stronę. Musi być między 1 a 50."
 *     ),
 *     @OA\Property(
 *         property="page",
 *         type="integer",
 *         example=1,
 *         description="Numer strony. Musi być co najmniej 1."
 *     ),
 *     @OA\Property(
 *         property="id",
 *         type="array",
 *         @OA\Items(
 *             type="integer",
 *             example=1
 *         ),
 *         description="Tablica identyfikatorów tagów. Każdy identyfikator musi być liczbą całkowitą i co najmniej 1."
 *     ),
 *     @OA\Property(
 *         property="order",
 *         type="object",
 *         @OA\Property(
 *             property="id",
 *             type="string",
 *             example="asc",
 *             description="Kierunek sortowania po identyfikatorze tagu. Musi być 'asc' lub 'desc'."
 *         ),
 *         @OA\Property(
 *             property="name",
 *             type="string",
 *             example="desc",
 *             description="Kierunek sortowania po nazwie tagu. Musi być 'asc' lub 'desc'."
 *         ),
 *         @OA\Property(
 *             property="tag",
 *             type="string",
 *             example="asc",
 *             description="Kierunek sortowania po tagu. Musi być 'asc' lub 'desc'."
 *         )
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="TagRequestPOST",
 *     type="object",
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         example="Tag Name",
 *         description="Nazwa tagu. To pole jest wymagane i musi mieć maksymalnie 255 znaków."
 *     ),
 *     @OA\Property(
 *         property="tag",
 *         type="string",
 *         example="unique-tag",
 *         description="Unikalny identyfikator tagu. To pole jest wymagane, musi być maksymalnie 255 znaków i być unikalne w tabeli tagów."
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         example="Opis tagu",
 *         description="Opis tagu. Opcjonalne pole."
 *     ),
 *     @OA\Property(
 *         property="icon",
 *         type="string",
 *         example="icon-url",
 *         description="Ikona tagu. Opcjonalne pole."
 *     ),
 *     @OA\Property(
 *         property="visibility",
 *         type="boolean",
 *         example=true,
 *         description="Flaga widoczności tagu. Opcjonalne pole."
 *     ),
 *     required={"name", "tag"}
 * )
 *
 * @OA\Schema(
 *     schema="TagRequestPUT",
 *     type="object",
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         example="Updated Tag Name",
 *         description="Nazwa tagu. To pole jest wymagane i musi mieć maksymalnie 255 znaków."
 *     ),
 *     @OA\Property(
 *         property="tag",
 *         type="string",
 *         example="unique-tag",
 *         description="Unikalny identyfikator tagu. To pole jest wymagane i musi być maksymalnie 255 znaków."
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         example="Updated tag description",
 *         description="Opis tagu. Opcjonalne pole."
 *     ),
 *     @OA\Property(
 *         property="icon",
 *         type="string",
 *         example="updated-icon-url",
 *         description="Ikona tagu. Opcjonalne pole."
 *     ),
 *     @OA\Property(
 *         property="visibility",
 *         type="boolean",
 *         example=false,
 *         description="Flaga widoczności tagu. Opcjonalne pole."
 *     ),
 *     required={"name", "tag"}
 * )
 *
 * @OA\Schema(
 *     schema="TagRequestDELETE",
 *     type="object",
 *     description="Schemat żądania dla metody DELETE w TagRequest. Brak określonych reguł walidacji."
 * )
 */
class TagRequest extends FormRequest
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
            'order.id' => 'string|in:asc,desc',
            'order.name' => 'string|in:asc,desc',
            'order.tag' => 'string|in:asc,desc',
        ],
        self::METHOD_POST => [
            'name' => 'required|string|max:255',
            'tag' => 'required|string|max:255|unique:tenant.tags,tag',
            'description' => 'nullable|string',
            'icon' => 'nullable',
            'visibility' => 'boolean',
        ],
        self::METHOD_PUT => [
            'name' => 'required|string|max:255',
            'tag' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable',
            'visibility' => 'boolean',
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
        if ('PUT' == $this->getMethod()) {
            $rules['tag'][] = Rule::unique('tags', 'tag')->ignore($this->id);
        }

        return self::$rules[$this->getMethod()] ?? [];
    }
}
