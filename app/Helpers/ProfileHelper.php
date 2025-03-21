<?php

namespace App\Helpers;

class ProfileHelper
{
    /**
     * generate avatar from name
     * 
     * @param string $name
     * @return string
     */
    public static function generateAvatarUrl($name, $size = 150)
    {
        return 'https://ui-avatars.com/api/?' . http_build_query([
            'name' => $name,
            'background' => 'random',
            'size' => $size,
            'font-size' => '0.5',
            'rounded' => 'true',
            'bold' => 'true',
        ]);
    }
} 