<?php

use App\Models\Filters\Filters;
use Illuminate\Database\Eloquent\Builder;

Builder::macro('filters', function (Filters $filters) {
    return $filters->getQuery($this);
});
