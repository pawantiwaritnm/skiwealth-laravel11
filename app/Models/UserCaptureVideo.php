<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserCaptureVideo extends Model
{
    protected $table = 'user_capture_video';

    protected $fillable = [
        'registration_id',
        'user_video',
        'image',
        'lat',
        'lng',
        'city',
        'state',
        'ip',
        'ipv_otp',
        'status',
    ];

    protected $casts = [
        'added_on' => 'datetime',
        'updated_on' => 'datetime',
        'status' => 'integer',
        'lat' => 'decimal:8',
        'lng' => 'decimal:8',
    ];

    const CREATED_AT = 'added_on';
    const UPDATED_AT = 'updated_on';

    /**
     * Get the registration that owns the IPV video.
     */
    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class, 'registration_id');
    }

    /**
     * Get location string.
     */
    public function getLocationString(): string
    {
        if ($this->city && $this->state) {
            return $this->city . ', ' . $this->state;
        }

        if ($this->city) {
            return $this->city;
        }

        if ($this->state) {
            return $this->state;
        }

        return 'Location not available';
    }

    /**
     * Check if location is available.
     */
    public function hasLocation(): bool
    {
        return !empty($this->lat) && !empty($this->lng);
    }

    /**
     * Get video URL.
     */
    public function getVideoUrl(): string
    {
        return $this->user_video ? asset('storage/' . $this->user_video) : '';
    }

    /**
     * Get image URL.
     */
    public function getImageUrl(): string
    {
        return $this->image ? asset('storage/' . $this->image) : '';
    }

    /**
     * Get formatted date.
     */
    public function getFormattedDate(): string
    {
        return $this->added_on ? $this->added_on->format('d M Y, h:i A') : '';
    }

    /**
     * Get full location details with coordinates.
     */
    public function getFullLocationDetails(): array
    {
        return [
            'city' => $this->city,
            'state' => $this->state,
            'latitude' => $this->lat,
            'longitude' => $this->lng,
            'has_location' => $this->hasLocation(),
            'location_string' => $this->getLocationString(),
        ];
    }
}
