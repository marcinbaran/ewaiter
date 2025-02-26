<?php

namespace App\Enum;

enum ApiError: string
{
    use EnumTrait;

    /* General */
    case INVALID_DATA = 'Invalid data';
    case ACTION_PROHIBITED = 'Action prohibited';
    case INCONSISTENT_DATA = 'Data inconsistency';
    case BAD_CONFIGURATION = 'Config data is missing entries or given entries have wrong types';
    case VALIDATION_FAILED = 'Submitted request did not pass validation';

    /* Auth */
    case INVALID_CREDENTIALS = 'The user credentials were incorrect';
    case PHONE_NUMBER_ALREADY_EXISTS = 'The phone number given already exists';
    case EMAIL_ALREADY_EXISTS = 'The email address given already exists';
    case ACTIVE_USER_NOT_FOUND = 'No such active user';
    case USER_NOT_FOUND = 'No such user';
    case USER_NOT_ACTIVE = 'The user is inactive';
    case USER_BLOCKED = 'The user is blocked';
    case TOO_MANY_ATTEMPTS_FOR_PASSWORD_RECOVERY = 'Too many attempts. Wait 15 minutes and try again.';
    case AUTH_SERVICE_HAS_GONE_AWAY = 'Auth service is not available';
    case INVALID_TOKEN = 'Invalid token';

    /* Address */
    case INVALID_PHONE_NUMBER = 'Phone number is invalid';

    /* SMS codes */
    case INVALID_SMS_CODE = 'The SMS code is incorrect';
    case CODE_EXPIRED = 'The SMS code expired';

    /* Restaurant */
    case RESTAURANT_IS_CLOSED = 'The restaurant is currently closed';
    case RESTAURANT_DOES_NOT_EXIST = 'Restaurant does not exist';
    case RESTAURANT_CONFIGURATION_HAVE_CHANGED = 'Restaurant configuration have changed. Please re-order.';

    /* Table */
    case TABLE_INACTIVE_OR_DOES_NOT_EXIST = 'Table is inactive or does not exist';

    /* Room */
    case ROOM_DOES_NOT_EXIST = 'Room does not exist';

    /* Order */
    case AVAILABILITY_OF_PRODUCTS_CHANGED = 'The availability of products has changed, please re-order';
    case MANDATORY_ADDITIONS_NOT_SELECTED = 'Some mandatory additions aren\'t selected';
    case MINIMUM_ORDER_VALUE_NOT_EXCEEDED = 'The required minimum order value was not exceeded';
    case NO_ADDRESS_PROVIDED = 'No address provided';

    /* Dish */
    case DISH_DETAILS_HAVE_CHANGED = 'Details of one or more dishes have changed.';

    /* Bundle */
    case BUNDLE_DETAILS_HAVE_CHANGED = 'Details of one or more bundles have changed.';

    /* Addition */
    case ADDITION_DETAILS_HAVE_CHANGED = 'Details of one or more additions have changed.';

    /* Payment */
    case USER_INACTIVE_CHOOSE_OTHER_PAYMENT_METHOD = 'Your account is still not activated, please choose other payment option';
    case PAYMENT_TYPE_NOT_AVAILABLE = 'Payment type is not available';
    case PAYMENT_TYPE_NOT_AVAILABLE_FOR_GUEST = 'Payment type is not available for guest';

    /* Points */
    case AVAILABILITY_OF_POINTS_CHANGED = 'The availability of points has changed, please re-order';
    case POINTS_CANNOT_BE_SEND = 'The points cannot be send';
    case POINTS_CONVERSION_RATE_HAS_CHANGED = 'The points conversion rate has changed';

    /* Vouchers */
    case INVALID_VOUCHER_CODE = 'The given voucher code is invalid';
    case VOUCHER_EXPIRED = 'The given voucher has expired';
    case VOUCHER_USED = 'The given voucher is used';

    /* Delivery */
    case ADDRESS_DELIVERY_SUSPENDED = 'Delivery to address is suspended';
    case DELIVERY_OPTION_IS_DISABLED = 'Chosen delivery option is disabled';
    case DELIVERY_ADDRESS_EXCEEDS_THE_RESTAURANT_RANGE = 'The selected delivery address exceeds the restaurants delivery range';
    case DELIVERY_ADDRESS_EXCEEDS_THE_RESTAURANT_POLYGON = 'The selected delivery address exceeds the restaurants delivery polygon';
    case NO_VALID_DELIVERY_TYPE = 'No valid delivery type';
    case ITEMS_CANNOT_BE_DELIVERED = 'Some items can\'t be selected for delivery';
    case DELIVERY_COST_COULD_NOT_BE_CALCULATED = 'Delivery cost could not be calculated due to delivery range configuration error.';

    /* Polygon */
    case RANGE_POLYGON_NOT_SET = 'Some delivery ranges do not have polygons set';


    case NO_DELIVERY_OPTIONS_AVAILABLE = 'No delivery options available';

    /* Friend */
    case FRIEND_USER_NOT_FOUND = 'Friend user not found';
    case ALREADY_FRIENDS = 'You already befriended that user';
    case INVITING_YOURSELF = 'You can\'t invite yourself';
    case USER_WAITS_FOR_YOUR_ACCEPT = 'User waits for your accept';
    case INVITATION_ALREADY_SENT = 'Invitation already sent';
    case RECEIVER_ID_MUST_BE_INTEGER = '\'receiver_id\' must be a positive integer';

    /* Setting */
    case SETTING_NOT_FOUND = 'Setting not found';

    /* Notifications */
    case TOO_MANY_WAITER_CALLS = 'There are too many waiter calls, please try again in 3 minutes.';

    /* Review */
    case REVIEW_ALREADY_ADDED = 'The review has already been added.';
    case COMMENT_TOO_LONG = 'Comment is too long.';
    case BILL_NOT_BELONG_TO_USER = 'The bill does not belong to this user.';
    case NON_DELIVERY_SHIPPING = 'The selected delivery is not for shipping.';
    case TIME_IS_UP = 'You cannot leave a review because more than 48 hours have passed since the order was placed.';
    case CAN_NOT_WRITE_REVIEW_YET = 'Can not write a review yet';
    case DELIVERY_RATING_ONLY_FOR_DELIVERY_ADDRESS = 'Delivery rating only for delivery to address';
    case REVIEW_COMMENT_EDITED = 'The review comment has already been edited';
    case REVIEW_RESTAURANT_COMMENT_EDITED = 'The review restaurant comment has already been edited';
    case OFFENSIVE_REVIEW = 'The review is offensive';

    /* Reservation */
    case TABLE_RESERVATION_DISABLED = 'Table reservation is disabled';

    /* FCM Token */
    case FCM_TOKEN_NOT_FOUND = 'FCM Token not found';

    /* TPay */
    case INVALID_CREDENTIAL = 'Invalid credential';

    /* Bill */
    case BILL_NOT_FOUND = 'Bill not found';
}
