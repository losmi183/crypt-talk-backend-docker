<?php

namespace App\Enums;

enum Status: string 
{
    case ACTIVE = 'active';
    case PENDING = 'pending_activation';
    case SUSPENDED = 'suspended';
}