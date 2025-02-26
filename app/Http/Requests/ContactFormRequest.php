<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
/**
 * @OA\Schema(
 *     schema="ContactFormRequest",
 *     type="object",
 *     title="Contact Form Request",
 *     description="Request body for submitting a contact form",
 *     required={"name", "email", "title", "message", "site_key"},
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="The name of the user",
 *         example="John Doe",
 *         minLength=2,
 *         maxLength=50
 *     ),
 *     @OA\Property(
 *         property="email",
 *         type="string",
 *         description="The email address of the user",
 *         format="email",
 *         example="john.doe@example.com",
 *         maxLength=50
 *     ),
 *     @OA\Property(
 *         property="title",
 *         type="string",
 *         description="The title of the message",
 *         example="Inquiry about services",
 *         minLength=3,
 *         maxLength=50
 *     ),
 *     @OA\Property(
 *         property="message",
 *         type="string",
 *         description="The content of the message",
 *         example="I would like to know more about your services.",
 *         minLength=3,
 *         maxLength=255
 *     ),
 *     @OA\Property(
 *         property="site_key",
 *         type="string",
 *         description="The site key for reCAPTCHA verification",
 *         example="6LcA_XXXXXAAAAA",
 *         minLength=1
 *     )
 * )
 */
class ContactFormRequest extends FormRequest
{
    private const string NAME_KEY = 'name';
    private const string EMAIL_KEY = 'email';
    private const string TITLE_KEY = 'title';
    private const string MESSAGE_KEY = 'message';
    private const string SITE_KEY = 'site_key';

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            self::NAME_KEY => 'required|string|min:2|max:50',
            self::EMAIL_KEY => 'required|string|email|max:50',
            self::TITLE_KEY => 'required|string|min:3|max:50',
            self::MESSAGE_KEY => 'required|string|min:3|max:255',
            self::SITE_KEY => 'required',
        ];
    }
}
