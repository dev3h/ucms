<?php

declare(strict_types=1);

namespace App\Consts;

/**
 * App constant.
 */
class AppConst
{
    # File management
    public const MIMES_FILE_ALLOWED = 'jpeg,png,jpg,gif,svg,pdf';
    public const MIMES_FILE_IDENTIFICATION_AGE_ALLOWED = 'png,jpg,pdf';

    public const MIMES_IMAGE_ALLOWED = 'jpeg,png,jpg,gif,svg';
    public const MIMES_FILES_ALLOWED = 'pdf,csv,mp3,mpeg,plain';
    public const MIMES_EXCEL_ALLOWED = 'vnd.openxmlformats-officedocument.spreadsheetml.sheet,vnd.ms-excel';

    public const MAX_SIZE_IMAGE = 5 * 1024; # Max size file image = 2MB
}
