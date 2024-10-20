<?php

namespace App\Enum;

enum StatusEnum: string {

    case DRAFT = 'draft';
    case PUBLISHED = 'published';
    case DELETED = 'deleted';
    
}