<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Casts\Attribute;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'mobile_no',
        'profile_image',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected function avatar(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                if (!empty($attributes['profile_image'])) {
                    // If profile_image starts with http/https, return as is
                    if (str_starts_with($attributes['profile_image'], 'http')) {
                        return $attributes['profile_image'];
                    }
                    // If it's a local file, prepend your storage URL
                    return asset('storage/' . $attributes['profile_image']);
                }
                
                // Generate UI Avatar with custom parameters
                return 'https://ui-avatars.com/api/?' . http_build_query([
                    'name' => $attributes['name'],
                    'background' => 'random',
                    'size' => '150', // Size in pixels
                    'font-size' => '0.5', // Relative to size
                    'rounded' => 'true',
                    'bold' => 'true'
                ]);
            }
        );
    }

     // Define the scopeActive method
     public function scopeActive($query)
     {
         return $query->where('status', 1);
     }
}
