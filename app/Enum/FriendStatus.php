<?php

namespace App\Enum;

enum FriendStatus: string
{


    case Requested = 'requested';
    case Accepted = 'accepted';
    case Rejected = 'rejected';
    case Blocked = 'blocked';
    case Pending = 'pending';
    case Removed = 'removed';
    case NotFound = 'not_found';
    case Cancelled = 'cancelled';


}
