<?php

namespace App\Http\Filters;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class BookingFilter
{
    protected static array $filters = [
        'status',
        'from_date',
        'to_date',
        'city_id',
    ];

    public static function apply($query, Request $request)
    {
        foreach (self::$filters as $filter) {
            if ($request->filled($filter)) {
                self::$filter($query, $request->$filter, $request);
            }
        }

        return $query;
    }

    protected static function status(Builder $query, $value, $request)
    {
        $query->where('status', $value);
    }

    protected static function start_date(Builder $query, $value, $request)
    {
        $query->whereDate('start_date', '>=', $value);
    }

    protected static function end_date(Builder $query, $value, $request)
    {
        $query->whereDate('end_date', '<=', $value);
    }

    protected static function city_id(Builder $query, $value, $request)
    {
        $query->whereHas('apartment', function ($q) use ($request) {
          $q->where('city_id', $request->city_id);
        });

    }

  }
