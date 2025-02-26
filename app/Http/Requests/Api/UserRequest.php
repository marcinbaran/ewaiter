<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\RequestTrait;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
/**
 * @OA\Schema(
 *     schema="UserRequestGET",
 *     type="object",
 *     @OA\Property(
 *         property="itemsPerPage",
 *         type="integer",
 *         description="Liczba elementów na stronę (max: 50).",
 *         example=10
 *     ),
 *     @OA\Property(
 *         property="page",
 *         type="integer",
 *         description="Numer strony.",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="id",
 *         type="array",
 *         @OA\Items(type="integer"),
 *         description="Tablica identyfikatorów użytkowników.",
 *         example={1, 2, 3}
 *     ),
 *     @OA\Property(
 *         property="order.id",
 *         type="string",
 *         description="Kolejność sortowania po identyfikatorze.",
 *         example="asc"
 *     ),
 *     @OA\Property(
 *         property="order.name",
 *         type="string",
 *         description="Kolejność sortowania po nazwisku.",
 *         example="desc"
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="UserRequestPOST",
 *     type="object",
 *     @OA\Property(
 *         property="first_name",
 *         type="string",
 *         description="Imię użytkownika.",
 *         example="Jan"
 *     ),
 *     @OA\Property(
 *         property="last_name",
 *         type="string",
 *         description="Nazwisko użytkownika.",
 *         example="Kowalski"
 *     ),
 *     @OA\Property(
 *         property="login",
 *         type="string",
 *         description="Login użytkownika. Musi być unikalny.",
 *         example="janek"
 *     ),
 *     @OA\Property(
 *         property="email",
 *         type="string",
 *         description="Adres e-mail użytkownika.",
 *         example="jan.kowalski@example.com"
 *     ),
 *     @OA\Property(
 *         property="password",
 *         type="string",
 *         description="Hasło użytkownika.",
 *         example="password123"
 *     ),
 *     @OA\Property(
 *         property="referred_user_id",
 *         type="integer",
 *         description="ID użytkownika, który polecił obecnego użytkownika.",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="roles",
 *         type="array",
 *         @OA\Items(type="string"),
 *         description="Tablica ról przypisanych do użytkownika.",
 *         example={"admin", "editor"}
 *     ),
 *     @OA\Property(
 *         property="blocked",
 *         type="boolean",
 *         description="Status blokady konta użytkownika.",
 *         example=true
 *     ),
 *     @OA\Property(
 *         property="isRoom",
 *         type="boolean",
 *         description="Czy użytkownik jest pokojem.",
 *         example=false
 *     ),
 *     @OA\Property(
 *         property="playerIds",
 *         type="array",
 *         @OA\Items(type="string"),
 *         description="Tablica identyfikatorów graczy.",
 *         example={"player1", "player2"}
 *     ),
 *     @OA\Property(
 *         property="table.name",
 *         type="string",
 *         description="Nazwa tabeli.",
 *         example="Table 1"
 *     ),
 *     @OA\Property(
 *         property="phone",
 *         type="string",
 *         description="Numer telefonu użytkownika. Musi być unikalny.",
 *         example="+48123456789"
 *     ),
 *     @OA\Property(
 *         property="birth_date",
 *         type="string",
 *         format="date",
 *         description="Data urodzenia użytkownika w formacie YYYY-MM-DD.",
 *         example="1990-01-01"
 *     ),
 *     @OA\Property(
 *         property="addresses",
 *         type="array",
 *         @OA\Items(
 *             type="object",
 *             @OA\Property(
 *                 property="company_name",
 *                 type="string",
 *                 description="Nazwa firmy.",
 *                 example="ACME Corp."
 *             ),
 *             @OA\Property(
 *                 property="nip",
 *                 type="integer",
 *                 description="Numer identyfikacji podatkowej.",
 *                 example=1234567890
 *             ),
 *             @OA\Property(
 *                 property="name",
 *                 type="string",
 *                 description="Imię adresata.",
 *                 example="Jan"
 *             ),
 *             @OA\Property(
 *                 property="surname",
 *                 type="string",
 *                 description="Nazwisko adresata.",
 *                 example="Kowalski"
 *             ),
 *             @OA\Property(
 *                 property="city",
 *                 type="string",
 *                 description="Miasto.",
 *                 example="Warszawa"
 *             ),
 *             @OA\Property(
 *                 property="postcode",
 *                 type="string",
 *                 description="Kod pocztowy.",
 *                 example="00-001"
 *             ),
 *             @OA\Property(
 *                 property="street",
 *                 type="string",
 *                 description="Ulica.",
 *                 example="Ul. Przykładowa"
 *             ),
 *             @OA\Property(
 *                 property="building_number",
 *                 type="string",
 *                 description="Numer budynku.",
 *                 example="10"
 *             ),
 *             @OA\Property(
 *                 property="house_number",
 *                 type="string",
 *                 description="Numer mieszkania.",
 *                 example="5B"
 *             ),
 *             @OA\Property(
 *                 property="floor",
 *                 type="string",
 *                 description="Piętro.",
 *                 example="2"
 *             ),
 *             @OA\Property(
 *                 property="phone",
 *                 type="string",
 *                 description="Numer telefonu kontaktowego.",
 *                 example="+48123456789"
 *             )
 *         )
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="UserRequestPUT",
 *     type="object",
 *     @OA\Property(
 *         property="first_name",
 *         type="string",
 *         description="Imię użytkownika.",
 *         example="Jan"
 *     ),
 *     @OA\Property(
 *         property="last_name",
 *         type="string",
 *         description="Nazwisko użytkownika.",
 *         example="Kowalski"
 *     ),
 *     @OA\Property(
 *         property="login",
 *         type="string",
 *         description="Login użytkownika. Musi być unikalny.",
 *         example="janek"
 *     ),
 *     @OA\Property(
 *         property="email",
 *         type="string",
 *         description="Adres e-mail użytkownika.",
 *         example="jan.kowalski@example.com"
 *     ),
 *     @OA\Property(
 *         property="password",
 *         type="string",
 *         description="Hasło użytkownika.",
 *         example="password123"
 *     ),
 *     @OA\Property(
 *         property="referred_user_id",
 *         type="integer",
 *         description="ID użytkownika, który polecił obecnego użytkownika.",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="roles",
 *         type="array",
 *         @OA\Items(type="string"),
 *         description="Tablica ról przypisanych do użytkownika.",
 *         example={"admin", "editor"}
 *     ),
 *     @OA\Property(
 *         property="blocked",
 *         type="boolean",
 *         description="Status blokady konta użytkownika.",
 *         example=true
 *     ),
 *     @OA\Property(
 *         property="isRoom",
 *         type="boolean",
 *         description="Czy użytkownik jest pokojem.",
 *         example=false
 *     ),
 *     @OA\Property(
 *         property="playerIds",
 *         type="array",
 *         @OA\Items(type="string"),
 *         description="Tablica identyfikatorów graczy.",
 *         example={"player1", "player2"}
 *     ),
 *     @OA\Property(
 *         property="table.name",
 *         type="string",
 *         description="Nazwa tabeli.",
 *         example="Table 1"
 *     ),
 *     @OA\Property(
 *         property="phone",
 *         type="string",
 *         description="Numer telefonu użytkownika. Musi być unikalny.",
 *         example="+48123456789"
 *     ),
 *     @OA\Property(
 *         property="birth_date",
 *         type="string",
 *         format="date",
 *         description="Data urodzenia użytkownika w formacie YYYY-MM-DD.",
 *         example="1990-01-01"
 *     ),
 *     @OA\Property(
 *         property="addresses",
 *         type="array",
 *         @OA\Items(
 *             type="object",
 *             @OA\Property(
 *                 property="company_name",
 *                 type="string",
 *                 description="Nazwa firmy.",
 *                 example="ACME Corp."
 *             ),
 *             @OA\Property(
 *                 property="nip",
 *                 type="integer",
 *                 description="Numer identyfikacji podatkowej.",
 *                 example=1234567890
 *             ),
 *             @OA\Property(
 *                 property="name",
 *                 type="string",
 *                 description="Imię adresata.",
 *                 example="Jan"
 *             ),
 *             @OA\Property(
 *                 property="surname",
 *                 type="string",
 *                 description="Nazwisko adresata.",
 *                 example="Kowalski"
 *             ),
 *             @OA\Property(
 *                 property="city",
 *                 type="string",
 *                 description="Miasto.",
 *                 example="Warszawa"
 *             ),
 *             @OA\Property(
 *                 property="postcode",
 *                 type="string",
 *                 description="Kod pocztowy.",
 *                 example="00-001"
 *             ),
 *             @OA\Property(
 *                 property="street",
 *                 type="string",
 *                 description="Ulica.",
 *                 example="Ul. Przykładowa"
 *             ),
 *             @OA\Property(
 *                 property="building_number",
 *                 type="string",
 *                 description="Numer budynku.",
 *                 example="10"
 *             ),
 *             @OA\Property(
 *                 property="house_number",
 *                 type="string",
 *                 description="Numer mieszkania.",
 *                 example="5B"
 *             ),
 *             @OA\Property(
 *                 property="floor",
 *                 type="string",
 *                 description="Piętro.",
 *                 example="2"
 *             ),
 *             @OA\Property(
 *                 property="phone",
 *                 type="string",
 *                 description="Numer telefonu kontaktowego.",
 *                 example="+48123456789"
 *             )
 *         )
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="UserRequestDELETE",
 *     type="object",
 *     description="Brak reguł walidacji dla metody DELETE."
 * )
 */
class UserRequest extends FormRequest
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
        ],
        self::METHOD_POST => [
            'first_name' => 'nullable|max:255',
            'last_name' => 'nullable|max:255',
            'login' => 'nullable|string|max:255|unique:users,login',
            'email' => 'required|email|max:255',
            'password' => 'required|min:6|max:100',
            'referred_user_id' => 'nullable|integer|exists:users,id',
            'roles' => 'array|min:1',
            'roles.*' => 'string',
            'blocked' => 'boolean',
            'isRoom' => 'boolean',
            'playerIds.*' => 'string|max:250',
            'table.name' => 'nullable|string|max:250',
            'phone' => 'nullable|string|unique:users,phone|max:20',
            'birth_date' => 'nullable|date_format:Y-m-d',

            'addresses.*.company_name' => 'nullable|string|max:100',
            'addresses.*.nip' => 'nullable|integer|digits:10',
            'addresses.*.name' => 'required|string|max:100',
            'addresses.*.surname' => 'nullable|string|max:100',
            'addresses.*.city' => 'required|string|max:100',
            'addresses.*.postcode' => 'required|string|max:10',
            'addresses.*.street' => 'nullable|string|max:100',
            'addresses.*.building_number' => 'nullable|string|max:50',
            'addresses.*.house_number' => 'nullable|string|max:50',
            'addresses.*.floor' => 'nullable|string|max:50',
            'addresses.*.phone' => 'nullable|string|max:15',
        ],
        self::METHOD_PUT => [
            'first_name' => 'nullable|max:255',
            'last_name' => 'nullable|max:255',
            'login' => 'nullable|string|max:255|unique:users,login',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'nullable|min:6|max:100',
            'referred_user_id' => 'nullable|integer|exists:users,id',
            'roles' => 'array|min:1',
            'roles.*' => 'string',
            'blocked' => 'boolean',
            'isRoom' => 'boolean',
            'playerIds.*' => 'string|max:250',
            'table.name' => 'nullable|string|max:250',
            'phone' => 'nullable|string|max:15|unique:users,phone',
            'birth_date' => 'nullable|date_format:Y-m-d',
            'addresses.*.company_name' => 'nullable|string|max:100',
            'addresses.*.nip' => 'nullable|integer|digits:10',
            'addresses.*.name' => 'required|string|max:100',
            'addresses.*.surname' => 'nullable|string|max:100',
            'addresses.*.city' => 'required|string|max:100',
            'addresses.*.postcode' => 'required|string|max:10',
            'addresses.*.street' => 'nullable|string|max:100',
            'addresses.*.building_number' => 'nullable|string|max:50',
            'addresses.*.house_number' => 'nullable|string|max:50',
            'addresses.*.floor' => 'nullable|string|max:50',
            'addresses.*.phone' => 'nullable|string|max:15',
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
        $rules = self::$rules[$this->getMethod()] ?? [];
        if ('PUT' == $this->getMethod()) {
            $rules['email'] = Rule::unique('users', 'email')->ignore($this->user->id);
            $rules['login'] = Rule::unique('users', 'login')->ignore($this->user->id);
        }

        if (env('APP_ENV', 'stagging')) {
            $rules['phone'] = 'nullable|string|max:15';
        }

        return $rules;
    }
}
