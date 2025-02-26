<div class="grid grid-cols-2 gap-4 text-gray-300">

    {{--        INFORMACJA O CZASIE ODPOWIEDZI --}}
    <div class="col-span-2 text-right -mt-8 text-gray-500 dark:text-white  text-sm break-words">
        @if (is_null($review->restaurant_comment) && !$review->timesToExpire['isRestaurantCommentExpired'])
            <p>{{__('admin.reviewsTable.comment_options.response_expiry')}} {{$review->timesToExpire['timeToExpireRestaurantComment']->format('d.m.Y H:i')}}</p>
        @elseif ($review->restaurant_edited&& $review->user_edited)
            <p>{{__('admin.reviewsTable.comment_options.all_expired')}}</p>
        @elseif (is_null($review->restaurant_comment) && $review->timesToExpire['isRestaurantCommentExpired'])
            <p>{{__('admin.reviewsTable.comment_options.expired')}}</p>
        @elseif (!is_null($review->restaurant_comment) && !$review->user_edited && !$review->timesToExpire['isUserEditExpired'])
            <p>{{__('admin.reviewsTable.comment_options.user_edit_expiry')}} {{$review->timesToExpire['timeToExpireUserEdited']->format('d.m.Y H:i')}}</p>
        @elseif (!is_null($review->restaurant_comment) && !$review->user_edited && $review->timesToExpire['isUserEditExpired'])
            <p>{{__('admin.reviewsTable.comment_options.expired')}}</p>
        @elseif (!is_null($review->restaurant_comment) && $review->user_edited && !$review->timesToExpire['isRestaurantEditExpired'])
            <p>{{__('admin.reviewsTable.comment_options.restaurant_edit_expiry')}} {{$review->timesToExpire['timeToExpireRestaurantEdited']->format('d.m.Y H:i')}}</p>
        @elseif (!is_null($review->restaurant_comment) && $review->user_edited && $review->timesToExpire['isRestaurantEditExpired'])
            <p>{{__('admin.reviewsTable.comment_options.expired')}}</p>
        @else
            <p>{{__('admin.reviewsTable.comment_options.expired')}}</p>
        @endif

    </div>
    {{--        INFORMACJA O OCENIE --}}
    <div class="col-span-2 flex space-x-4 p-2">
        <div class="data-field">
            <label for="rating_food" class="block text-sm font-medium text-gray-500 dark:text-white break-words">
                {{__('admin.reviewsTable.rating_food')}}:</label>
            <p id="rating_food" class="mt-1 text-lg text-yellow-400">

            <div class="container">
                <div>
                    @php
                        use App\Decorators\StarDecorator;
                        $ratingFood =  $review['rating_food'] ;
                        echo StarDecorator::decorate($ratingFood);
                    @endphp
                </div>
            </div>
            </p>
        </div>

        @if(!is_null($review['rating_delivery']))
            <div class="data-field">
                <label for="rating_delivery"
                       class="block text-sm font-medium text-gray-500 dark:text-white break-words">{{__('admin.reviewsTable.rating_delivery')}}
                    :</label>
                <p id="rating_delivery" class="mt-1 text-lg text-yellow-400">
                <div class="container">
                    <div>
                        @php
                            $delivery_rating = $review['rating_delivery'];
                            echo StarDecorator::decorate($delivery_rating);
                        @endphp
                    </div>
                </div>
                </p>
            </div>
        @endif
    </div>
    {{--        INFORMACJA O KOMENTARACH --}}
    <div class="data-field col-span-1">
        <div class="flex justify-between items-center mb-1">
            <label for="comment"
                   class="inline-block font-medium text-gray-500 dark:text-white text-sm break-words">{{__('admin.reviewsTable.comment')}}

            </label>

            <div class="flex items-center space-x-2">
                @if($review->user_edited)
                    <label for="comment"
                           class="inline-block font-medium text-gray-500 dark:text-white text-sm break-words">{{__('admin.reviewsTable.edited')}}</label>
                @endif
                @if(!is_null($review->restaurant_comment) && !$review->user_edited)
                    <label for="comment"
                           class="inline-block font-medium text-gray-500 dark:text-white text-sm break-words">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M10.3333 6.44444L8 8V4.11111M1 8C1 8.91925 1.18106 9.82951 1.53284 10.6788C1.88463 11.5281 2.40024 12.2997 3.05025 12.9497C3.70026 13.5998 4.47194 14.1154 5.32122 14.4672C6.1705 14.8189 7.08075 15 8 15C8.91925 15 9.82951 14.8189 10.6788 14.4672C11.5281 14.1154 12.2997 13.5998 12.9497 12.9497C13.5998 12.2997 14.1154 11.5281 14.4672 10.6788C14.8189 9.82951 15 8.91925 15 8C15 7.08075 14.8189 6.1705 14.4672 5.32122C14.1154 4.47194 13.5998 3.70026 12.9497 3.05025C12.2997 2.40024 11.5281 1.88463 10.6788 1.53284C9.82951 1.18106 8.91925 1 8 1C7.08075 1 6.1705 1.18106 5.32122 1.53284C4.47194 1.88463 3.70026 2.40024 3.05025 3.05025C2.40024 3.70026 1.88463 4.47194 1.53284 5.32122C1.18106 6.1705 1 7.08075 1 8Z"
                                stroke="#9CA3AF" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </label>
                @endif
            </div>
        </div>
        <textarea id="comment"
                  class="resize-none mt-1 text-lg rounded-md p-2 h-44 w-full dark:bg-gray-700 text-gray-900 bg-gray-100 border-gray-300 dark:text-gray-50 dark:border-gray-700 bg-white"
                  maxlength="1000"
                  disabled>{{ $review->comment }}</textarea>
    </div>


    <form id="reviewForm" action="{{ route('admin.reviews.update', ['review' =>$review->id]) }}"
          method="POST">
        @csrf
        @method('PATCH')
        <div class="data-field col-span-1">
            <div class="flex justify-between items-center mb-1">
                <label for="restaurant_comment"
                       class="inline-block font-medium text-gray-500 dark:text-white text-sm break-words">
                    {{__('admin.reviewsTable.restaurant_comment')}}:
                </label>
                <div class="flex items-center space-x-2">
                    @if($review->restaurant_edited)
                        <label for="restaurant_comment"
                               class="inline-block font-medium text-gray-500 dark:text-white text-sm break-words">
                            {{__('admin.reviewsTable.edited')}}
                        </label>
                    @endif
                    @if(($review->user_edited || is_null($review->restaurant_comment)) && !$review->restaurant_edited)
                        <label for="restaurant_comment"
                               class="inline-block font-medium text-gray-500 dark:text-white text-sm break-words">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M10.3333 6.44444L8 8V4.11111M1 8C1 8.91925 1.18106 9.82951 1.53284 10.6788C1.88463 11.5281 2.40024 12.2997 3.05025 12.9497C3.70026 13.5998 4.47194 14.1154 5.32122 14.4672C6.1705 14.8189 7.08075 15 8 15C8.91925 15 9.82951 14.8189 10.6788 14.4672C11.5281 14.1154 12.2997 13.5998 12.9497 12.9497C13.5998 12.2997 14.1154 11.5281 14.4672 10.6788C14.8189 9.82951 15 8.91925 15 8C15 7.08075 14.8189 6.1705 14.4672 5.32122C14.1154 4.47194 13.5998 3.70026 12.9497 3.05025C12.2997 2.40024 11.5281 1.88463 10.6788 1.53284C9.82951 1.18106 8.91925 1 8 1C7.08075 1 6.1705 1.18106 5.32122 1.53284C4.47194 1.88463 3.70026 2.40024 3.05025 3.05025C2.40024 3.70026 1.88463 4.47194 1.53284 5.32122C1.18106 6.1705 1 7.08075 1 8Z"
                                    stroke="#9CA3AF" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </label>
                    @endif
                </div>
            </div>


            <textarea id="restaurant_comment" name="restaurant_comment" maxlength="1000"
                      class="focus:border-rose-500 dark:focus:border-rose-500 resize-none mt-1 text-lg rounded-md p-2 h-44 w-full dark:bg-gray-700 text-gray-900 bg-gray-100 border-gray-300 dark:text-gray-50 dark:border-gray-700 bg-white"
                      placeholder="-"></textarea>

        </div>

        {{--        Przyciski do edycji--}}
        <div class="col-span-2 text-right mt-4">
            <div class="relative w-full my-6 group flex justify-end">
                <button id="reset" type="reset" class="text-sm focus:ring-2 font-medium rounded-lg text-gray-50 hover:underline dark:text-gray-50 px-5 py-2 flex justify-center items-center text-gray-50  bg-gray-600 hover:bg-gray-500 focus:ring-gray-400 dark:bg-gray-700
        dark:hover:bg-gray-600 dark:focus:ring-gray-900 mr-2">
                    {{__('admin.Clear')}}
                </button>

                <button id="save-button" type="submit"
                        class="text-sm focus:ring-2 font-medium rounded-lg text-gray-50 hover:underline dark:text-gray-50 px-5 py-2 flex justify-center items-center text-gray-50 bg-primary-900 hover:bg-primary-800 focus:ring-primary-700 dark:bg-primary-700 dark:hover:bg-primary-800 dark:focus:ring-primary-900 flex flex-row justify-between">
                    {{__('admin.Submit')}}
                    <span class="button-form"></span>
                </button>
            </div>
        </div>
    </form>
</div>
@section('bottomscripts')
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const review = @json($review);
            const textAreaElement = document.getElementById("restaurant_comment");
            const saveButtonElement = document.getElementById("save-button");
            const resetButtonElement = document.getElementById("reset");

            textAreaElement.value = review.restaurant_comment;
            resetButtonElement.addEventListener("click", () => {
                textAreaElement.value = "";
            });

            textAreaElement.addEventListener("input", (event) => {
                if (event.target.value === "") {
                    disableElement(resetButtonElement);
                } else {
                    enableElement(resetButtonElement);
                }
            });

            if (!editingRestaurantCommentIsEligible(review)) {
                disableElement(textAreaElement);
                disableElement(saveButtonElement);
                disableElement(resetButtonElement);
            } else {
                enableElement(textAreaElement);
                enableElement(saveButtonElement);
                if (!!review.restaurant_comment) {
                    enableElement(resetButtonElement);
                } else {
                    disableElement(resetButtonElement);
                }
            }
        });

        const editingRestaurantCommentIsEligible = (review) => {

            // Sprawdź, czy komentarz restauracji nie wygasł
            if (review.timesToExpire.isRestaurantCommentExpired) {
                return false;
            }

            // Sprawdź, czy oba edycje (restauracja i użytkownik) wygasły
            if (review.restaurant_edited && review.user_edited) {
                return false;
            }

            // Brak komentarza restauracji i komentarz nie wygasł
            if (review.restaurant_comment === null && !review.timesToExpire.isRestaurantCommentExpired) {
                return true;
            }

            // Komentarz restauracji istnieje, użytkownik nie edytował, a czas na edycję użytkownika nie wygasł
            if (
                review.restaurant_comment !== null &&
                !review.user_edited &&
                !review.timesToExpire.isUserEditExpired
            ) {
                return false;
            }

            // Komentarz restauracji istnieje, użytkownik nie edytował, a czas na edycję użytkownika wygasł
            if (
                review.restaurant_comment !== null &&
                !review.user_edited &&
                review.timesToExpire.isUserEditExpired
            ) {
                return false;
            }

            // Komentarz restauracji istnieje, użytkownik edytował, a czas na edycję restauracji nie wygasł
            if (
                review.restaurant_comment !== null &&
                review.user_edited &&
                !review.timesToExpire.isRestaurantEditExpired
            ) {
                return true;
            }

            // Komentarz restauracji istnieje, użytkownik edytował, a czas na edycję restauracji wygasł
            if (
                review.restaurant_comment !== null &&
                review.user_edited &&
                review.timesToExpire.isRestaurantEditExpired
            ) {
                return false;
            }

            // Domyślnie nie uprawniony
            return false;
        };
        const disableElement = (element) => {
            element.classList.add("cursor-not-allowed");
            element.disabled = true;
        };

        const enableElement = (element) => {
            element.classList.remove("cursor-not-allowed");
            element.disabled = false;
        };
    </script>
@endsection
