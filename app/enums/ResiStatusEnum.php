<?php

namespace App\Enums;

enum ResiStatusEnum: string
{
    case DATA_ENTRY = 'Data Entry';
    case PICKED_UP = 'Picked Up';
    case PENDING = 'Pending';

    case PROCESSING = 'Processing';
    case IN_TRANSIT = 'In Transit';
    case DELIVERED = 'Delivered';
    case RETURNED = 'Returned';
    case CANCELLED = 'Cancelled';
}
