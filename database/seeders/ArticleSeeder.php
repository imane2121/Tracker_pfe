<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Article;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        // Create common tags for marine waste articles
        $tags = [
            'Ocean Pollution',
            'Marine Life',
            'Plastic Waste',
            'Beach Cleanup',
            'Environmental Impact',
            'Sustainability',
            'Conservation',
            'Community Action',
            'Recycling',
            'Volunteer',
            'Climate Change',
            'Coastal Protection'
        ];

        $tagModels = [];
        foreach ($tags as $tagName) {
            $tagModels[] = Tag::firstOrCreate(
                ['name' => $tagName],
                ['slug' => Str::slug($tagName)]
            );
        }

        // Get all admin users for authors
        $authors = User::whereHas('roles', function($query) {
            $query->whereIn('title', ['admin', 'supervisor']);
        })->get();

        // If no authors found, use the first user as fallback
        if ($authors->isEmpty()) {
            $authors = User::take(1)->get();
        }

        // Sample article content templates
        $articleTemplates = [
            [
                'category' => 'educational',
                'title' => 'Understanding Marine Waste: Types and Impact',
                'content' => "Marine waste is one of the most pressing environmental challenges of our time. This comprehensive guide explains the different types of marine waste and their impact on ocean ecosystems...",
            ],
            [
                'category' => 'awareness',
                'title' => 'The Hidden Danger of Microplastics in Our Oceans',
                'content' => "Microplastics have become an invisible threat to marine life. This article explores how these tiny particles enter our oceans and their devastating effects on the marine ecosystem...",
            ],
            [
                'category' => 'best_practices',
                'title' => 'Best Practices for Beach Cleanup Operations',
                'content' => "Organizing an effective beach cleanup requires careful planning and proper execution. Here are the best practices to ensure maximum impact while ensuring safety...",
            ],
            [
                'category' => 'initiative',
                'title' => 'Local Heroes: Community-Led Marine Conservation',
                'content' => "Discover how local communities along Morocco's coastline are taking action to protect marine environments. These inspiring stories showcase the power of community-driven conservation...",
            ],
            [
                'category' => 'report',
                'title' => '2024 Marine Waste Statistics: A Growing Crisis',
                'content' => "Our latest report reveals alarming trends in marine waste accumulation along Morocco's coastline. Key findings include increased plastic pollution and emerging hotspots...",
            ],
            [
                'category' => 'event',
                'title' => 'Join the Nationwide Beach Cleanup Campaign',
                'content' => "Mark your calendars for the biggest beach cleanup event of the year. This coordinated effort will span multiple coastal regions and needs your support...",
            ]
        ];

        // Create multiple variations of each template
        foreach ($articleTemplates as $template) {
            foreach (range(1, 3) as $i) {
                $publishedAt = Carbon::now()->subDays(rand(0, 60));
                
                $article = Article::create([
                    'title' => $i === 1 ? $template['title'] : $template['title'] . " (Part $i)",
                    'content' => $template['content'] . "\n\nPart $i of our series explores " . Str::random(100),
                    'category' => $template['category'],
                    'author_id' => $authors->random()->id,
                    'published_at' => $publishedAt,
                    'is_featured' => rand(0, 5) === 0, // 20% chance of being featured
                    'view_count' => rand(50, 1000),
                    'metadata' => json_encode([
                        'reading_time' => rand(3, 15) . ' minutes',
                        'related_links' => [
                            'https://example.com/marine-conservation',
                            'https://example.com/ocean-cleanup'
                        ]
                    ])
                ]);

                // Attach 2-4 random tags to each article
                $article->tags()->attach(
                    collect($tagModels)->random(rand(2, 4))->pluck('id')->toArray()
                );
            }
        }
    }
} 