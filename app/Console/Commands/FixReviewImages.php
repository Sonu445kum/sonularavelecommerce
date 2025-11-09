<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Review;

class FixReviewImages extends Command
{
    protected $signature = 'reviews:fix-images';
    protected $description = 'Fix existing review images stored incorrectly in DB';

    public function handle()
    {
        $reviews = Review::all();
        $this->info("Found {$reviews->count()} reviews. Fixing...");

        foreach ($reviews as $review) {
            $images = $review->images;

            // Decode JSON if string
            if (is_string($images)) {
                $images = json_decode($images, true);
            }

            // Ensure we have an array
            if (!is_array($images)) {
                $images = [];
            }

            $fixedImages = [];

            foreach ($images as $img) {
                if (empty($img)) continue;

                // Remove escape slashes and quotes
                $img = str_replace(['\\', '"'], '', $img);

                // Remove full URLs like http://localhost/storage/
                $img = preg_replace('#https?://[^/]+/storage/#i', '', $img);

                // Remove leading/trailing slashes and spaces
                $img = trim($img, '/ ');

                // Normalize duplicate slashes
                $img = preg_replace('#/+#', '/', $img);

                if (!empty($img)) {
                    $fixedImages[] = $img;
                }
            }

            // Save cleaned array as JSON
            $review->images = $fixedImages;
            $review->save();

            $this->info("✅ Fixed review ID {$review->id}");
        }

        $this->info("🎉 All review images fixed successfully!");
        return 0;
    }
}
