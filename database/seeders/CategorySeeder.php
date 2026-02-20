<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        
        $categories = [
            [
                'name' => 'Anime / Manga',
                'slug' => 'anime-manga',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Fantasy',
                'slug' => 'fantasy',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Comic',
                'slug' => 'comic',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Novel',
                'slug' => 'novel',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Sci-Fi',
                'slug' => 'sci-fi',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Drama',
                'slug' => 'drama',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Thriller',
                'slug' => 'thriller',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Social',
                'slug' => 'social',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Family',
                'slug' => 'family',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Romance',
                'slug' => 'romance',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Horror',
                'slug' => 'horror',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Mystery',
                'slug' => 'mystery',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Adventure',
                'slug' => 'adventure',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Historical',
                'slug' => 'historical',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Biography',
                'slug' => 'biography',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Self-Help',
                'slug' => 'self-help',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Business',
                'slug' => 'business',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Technology',
                'slug' => 'technology',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Science',
                'slug' => 'science',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Humor',
                'slug' => 'humor',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        // Using insert for better performance
        Category::insert($categories);
    }
}