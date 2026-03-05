<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationPreference extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'email_enabled',
        'whatsapp_enabled',
        'sms_enabled',
        'push_enabled'
    ];

    protected $casts = [
        'email_enabled' => 'boolean',
        'whatsapp_enabled' => 'boolean',
        'sms_enabled' => 'boolean',
        'push_enabled' => 'boolean',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeEnabled($query, $method)
    {
        return $query->where("{$method}_enabled", true);
    }

    // Static methods
    public static function getDefaultPreferences()
    {
        return [
            'payment_confirmation' => [
                'email_enabled' => true,
                'whatsapp_enabled' => true,
                'sms_enabled' => false,
                'push_enabled' => true,
            ],
            'assessment_result' => [
                'email_enabled' => true,
                'whatsapp_enabled' => true,
                'sms_enabled' => false,
                'push_enabled' => true,
            ],
            'certificate_issued' => [
                'email_enabled' => true,
                'whatsapp_enabled' => true,
                'sms_enabled' => false,
                'push_enabled' => true,
            ],
            'course_reminder' => [
                'email_enabled' => true,
                'whatsapp_enabled' => true,
                'sms_enabled' => false,
                'push_enabled' => true,
            ],
            'batch_start' => [
                'email_enabled' => true,
                'whatsapp_enabled' => true,
                'sms_enabled' => false,
                'push_enabled' => true,
            ],
            'payment_due' => [
                'email_enabled' => true,
                'whatsapp_enabled' => true,
                'sms_enabled' => true,
                'push_enabled' => true,
            ],
            'course_completion' => [
                'email_enabled' => true,
                'whatsapp_enabled' => true,
                'sms_enabled' => false,
                'push_enabled' => true,
            ],
        ];
    }

    public static function createDefaultPreferences($userId)
    {
        $defaults = self::getDefaultPreferences();
        
        foreach ($defaults as $type => $preferences) {
            self::create([
                'user_id' => $userId,
                'type' => $type,
                ...$preferences
            ]);
        }
    }

    public static function getUserPreferences($userId)
    {
        $preferences = self::where('user_id', $userId)->get()->keyBy('type');
        $defaults = self::getDefaultPreferences();
        
        // Merge with defaults for any missing preferences
        foreach ($defaults as $type => $default) {
            if (!$preferences->has($type)) {
                $preferences->put($type, new self([
                    'user_id' => $userId,
                    'type' => $type,
                    ...$default
                ]));
            }
        }
        
        return $preferences;
    }
}
