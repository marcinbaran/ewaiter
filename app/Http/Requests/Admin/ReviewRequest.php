<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\Api\ReviewRequest as ApiReviewRequest;

class ReviewRequest extends ApiReviewRequest
{

    public function attributes()
    {
        return [
            self::RESTAURANT_COMMENT => __('validation.review.restaurant_comment'),
        ];
    }

    public function rules()
    {
        $rules = [
            self::METHOD_GET => [
                self::ID_KEY => 'nullable|integer',
                self::RESTAURANT_ID_KEY => 'nullable|integer',
                self::BILL_ID_KEY => 'nullable|integer',
            ],
            self::METHOD_POST => [
                self::RESTAURANT_ID_KEY => 'required|integer',
                self::BILL_ID_KEY => 'required|integer',
                self::RATING_FOOD_KEY => 'required|integer|between:1,5',
                self::RATING_DELIVERY_KEY => 'nullable|integer|between:1,5',
                self::COMMENT_KEY => 'nullable|string|max:1000|min:3',
                self::RESTAURANT_COMMENT => 'nullable|string|max:1000|min:3',
            ],
            self::METHOD_PUT => [
                self::ID_KEY => 'required|integer',
                self::RATING_FOOD_KEY => 'required|integer|between:1,5',
                self::RATING_DELIVERY_KEY => 'nullable|integer|between:1,5',
                self::COMMENT_KEY => 'nullable|string|max:1000|min:3',
                self::RESTAURANT_COMMENT => 'nullable|string|max:1000|min:3',
            ],
            self::METHOD_PATCH => [
                self::RESTAURANT_COMMENT => 'nullable|string|max:1000|min:3',
            ],
        ];

        return $rules[$this->getMethod()];
    }

}
