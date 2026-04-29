<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimeCondition extends Model
{
    protected $fillable = [
        'name',
        'time_start',
        'time_end',
        'days_of_week',
        'days_of_month',
        'months',
        'year',
        'holidays_enabled',
        'holiday_dates',
        'timezone',
        'destination_true',
        'destination_false',
        'enabled',
    ];

    protected $casts = [
        'days_of_week' => 'array',
        'days_of_month' => 'array',
        'months' => 'array',
        'holiday_dates' => 'array',
        'holidays_enabled' => 'boolean',
        'enabled' => 'boolean',
        'time_start' => 'datetime:H:i',
        'time_end' => 'datetime:H:i',
    ];

    /**
     * Check if current time matches this time condition
     */
    public function matchesCurrentTime(): bool
    {
        if (! $this->enabled) {
            return false;
        }

        $now = now($this->timezone ?? 'UTC');

        // Check time range
        if ($this->time_start && $this->time_end) {
            $currentTime = $now->format('H:i');
            $startTime = $this->time_start->format('H:i');
            $endTime = $this->time_end->format('H:i');

            if ($currentTime < $startTime || $currentTime > $endTime) {
                return false;
            }
        }

        // Check days of week
        if ($this->days_of_week && ! in_array($now->dayOfWeek, $this->days_of_week)) {
            return false;
        }

        // Check days of month
        if ($this->days_of_month && ! in_array($now->day, $this->days_of_month)) {
            return false;
        }

        // Check months
        if ($this->months && ! in_array($now->month, $this->months)) {
            return false;
        }

        // Check year
        if ($this->year && $now->year != $this->year) {
            return false;
        }

        // Check holidays
        if ($this->holidays_enabled && $this->holiday_dates) {
            $today = $now->format('m-d');
            if (in_array($today, $this->holiday_dates)) {
                return false; // It's a holiday, so condition doesn't match
            }
        }

        return true;
    }
}
