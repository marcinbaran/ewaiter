<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactFormRequest;
use App\Mail\ContactFormMail;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use stdClass;
use Symfony\Component\HttpFoundation\Response;
/**
 * @OA\Post(
 *     path="/api/contact",
 *     operationId="submitContactForm",
 *     tags={"Contact"},
 *     summary="Submit a contact form",
 *     description="Submits a contact form with reCAPTCHA verification.",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(ref="#/components/schemas/ContactFormRequest")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Form submitted successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="The form has been successfully submitted"
 *             ),
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="name", type="string", example="John Doe"),
 *                 @OA\Property(property="email", type="string", example="john.doe@example.com"),
 *                 @OA\Property(property="title", type="string", example="Inquiry about services"),
 *                 @OA\Property(property="message", type="string", example="I would like to know more about your services."),
 *                 @OA\Property(property="site_key", type="string", example="6LcA_XXXXXAAAAA")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Unprocessable Entity",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(
 *                 property="message",
 *                 type="string",
 *                 example="reCAPTCHA verification failed"
 *             )
 *         )
 *     )
 * )
 */
class ContactFormController extends Controller
{
    private const string RECAPTCHA_VERIFY_URL = 'https://www.google.com/recaptcha/api/siteverify';
    private const string EMAIL_KEY = 'email';
    private const string MESSAGE_KEY = 'message';
    private const string SITE_KEY = 'site_key';
    private const string SECRET_KEY = 'secret';
    private const string RESPONSE_KEY = 'response';
    private const string DATA_KEY = 'data';

    public function __invoke(ContactFormRequest $request): JsonResponse
    {
        $contactFormRequestData = $request->all();
        $responseBody = $this->getReCaptchaResponse($contactFormRequestData[self::SITE_KEY]);

        if (!$responseBody->success) {
            return response()->json([self::MESSAGE_KEY => 'reCAPTCHA verification failed'], 422);
        }

        $yourEmail = env('CONTACT_MAIL_USERNAME');


        Mail::mailer('contact_smtp')
            ->to($yourEmail)
            ->send(new ContactFormMail($contactFormRequestData));

        return response()->json([
            self::MESSAGE_KEY => 'The form has been successfully submitted',
            self::DATA_KEY => $contactFormRequestData
        ], Response::HTTP_OK);
    }

    private function getReCaptchaResponse(string $validatedData): stdClass
    {
        $recaptchaSecret = config('google-captcha.nocaptcha.secret');

        $recaptchaData = [
            self::SECRET_KEY => $recaptchaSecret,
            self::RESPONSE_KEY => $validatedData,
        ];

        $response = Http::asForm()->post(self::RECAPTCHA_VERIFY_URL, $recaptchaData);

        return json_decode($response->getBody());
    }
}
