<?php

declare(strict_types=1);

namespace App\Exceptions;

class NoDataException extends BaseException
{
    /**
     * {@inheritdoc}
     */
    public function toResponse($request)
    {
        $this->setErrorMessage(__('errors.no_data'));
        $this->setErrors('');
        $this->setErrorCode(404);

        return parent::toResponse($request);
    }
}
