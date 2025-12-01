<?php

namespace App\Modules\Transfer\Enums;

enum NotificationChannelEnum: string
{
    case EMAIL = 'email';
    case SMS = 'sms';
}
