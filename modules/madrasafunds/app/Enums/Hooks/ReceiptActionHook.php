<?php

declare(strict_types=1);

namespace Modules\MadrasaFunds\Enums\Hooks;

enum ReceiptActionHook: string
{
    case CREATED_BEFORE = 'action.madrasafunds.receipt.created_before';
    case CREATED_AFTER = 'action.madrasafunds.receipt.created_after';
    case UPDATED_AFTER = 'action.madrasafunds.receipt.updated_after';
    case CANCELLED_AFTER = 'action.madrasafunds.receipt.cancelled_after';
    case DELETED_AFTER = 'action.madrasafunds.receipt.deleted_after';
}
