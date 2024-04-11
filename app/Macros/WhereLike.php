<?php

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

$callable = function (Builder $query, $columns, $value, $orWhere) {
    $query->where(function (Builder $query) use ($columns, $value, $orWhere) {
        foreach (Arr::wrap($columns) as $column) {
            $query->when(
                Str::contains($column, '.'),
                // search relation
                function (Builder $query) use ($column, $value) {
                    $parts = explode('.', $column);
                    $relationColumn = array_pop($parts);
                    $relationName = join('.', $parts);

                    $query->orWhereHas($relationName, function (Builder $query) use ($relationColumn, $value) {
                        $query->where($relationColumn, 'LIKE', "%{$value}%");
                    });
                },
                // search default
                function (Builder $query) use ($column, $value, $orWhere) {
                    $query->{$orWhere ? 'orWhere' : 'where'}($column, 'LIKE', "%{$value}%");
                }
            );
        }
    });
};

Builder::macro('whereLike', function ($column, $value, $filter = false) {

    $escaped = str_replace(['\\', '_', '%'], ['\\\\', '\\_', '\\%'], $value);

    $isJapanese = preg_match('/[\x{4E00}-\x{9FBF}\x{3040}-\x{309F}\x{30A0}-\x{30FF}]/u', $value);

    if ($isJapanese && !Str::contains($column, '->') && $filter == true) {
        return $this->where($column, 'LIKE', "%$escaped%");
    } elseif ($isJapanese && !Str::contains($column, '->')) {
        return $this->where(\DB::raw("BINARY `$column`"), 'LIKE', "%$escaped%");
    } else {
        return $this->where($column, 'LIKE', "%$escaped%");
    }
});

Builder::macro('orWhereLike', function ($column, $value, $filter = false) {
    $escaped = str_replace(['\\', '_', '%'], ['\\\\', '\\_', '\\%'], $value);

    $isJapanese = preg_match('/[\x{4E00}-\x{9FBF}\x{3040}-\x{309F}\x{30A0}-\x{30FF}]/u', $value);

    if ($isJapanese && !Str::contains($column, '->') && $filter == true) {
        return $this->orWhere($column, 'LIKE', "%$escaped%");
    } elseif ($isJapanese && !Str::contains($column, '->')) {
        return $this->orWhere(\DB::raw("BINARY `$column`"), 'LIKE', "%$escaped%");
    } else {
        return $this->orWhere($column, 'LIKE', "%$escaped%");
    }
});
