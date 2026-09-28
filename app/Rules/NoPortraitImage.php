<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Intervention\Image\Facades\Image;

class NoPortraitImage implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // Ensure that the uploaded image is a landscape image
        $image = Image::make($value);
        $width = $image->width();
        $height = $image->height();
        $threshold = 1.0; // Adjust this value as needed

        return $width / $height >= $threshold;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute must not be a Landscape image.';
    }
}
