<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'profile_picture',
        'last_seen',
        'is_online',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_seen' => 'datetime',
        'is_online' => 'boolean',
        'password' => 'hashed',
    ];

    /**
     * Get messages sent by this user
     */
    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    /**
     * Get messages received by this user
     */
    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    /**
     * Get all conversations for this user
     */
    public function conversations()
    {
        return $this->belongsToMany(User::class, 'messages', 'sender_id', 'receiver_id')
                    ->orWhere('messages.receiver_id', $this->id)
                    ->distinct();
    }

    /**
     * Get unread messages count for this user
     */
    public function unreadMessagesCount()
    {
        return $this->receivedMessages()->where('is_read', false)->count();
    }

    /**
     * Get conversation with specific user
     */
    public function conversationWith($userId)
    {
        return Message::where(function ($query) use ($userId) {
            $query->where('sender_id', $this->id)
                  ->where('receiver_id', $userId);
        })->orWhere(function ($query) use ($userId) {
            $query->where('sender_id', $userId)
                  ->where('receiver_id', $this->id);
        })->orderBy('created_at', 'asc');
    }

    /**
     * Mark user as online
     */
    public function markAsOnline()
    {
        $this->update([
            'is_online' => true,
            'last_seen' => now()
        ]);
    }

    /**
     * Mark user as offline
     */
    public function markAsOffline()
    {
        $this->update([
            'is_online' => false,
            'last_seen' => now()
        ]);
    }
}