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
        // Create detailed tags
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
            'Coastal Protection',
            'Marine Education',
            'Waste Management',
            'Environmental Policy',
            'Scientific Research'
        ];

        $tagModels = [];
        foreach ($tags as $tagName) {
            $tagModels[] = Tag::firstOrCreate(
                ['name' => $tagName],
                ['slug' => Str::slug($tagName)]
            );
        }

        // Get authors
        $authors = User::whereHas('roles', function($query) {
            $query->whereIn('title', ['admin', 'supervisor']);
        })->get();

        if ($authors->isEmpty()) {
            $authors = User::take(1)->get();
        }

        // Enhanced article templates with detailed content
        $articleTemplates = [
            [
                'category' => 'educational',
                'title' => 'Understanding Marine Waste: A Comprehensive Guide',
                'content' => "<h2>Introduction to Marine Waste</h2>
                    <p>Marine waste represents one of the most significant environmental challenges of the 21st century. This comprehensive guide explores the various types of marine debris, their sources, and their impact on ocean ecosystems.</p>
                    
                    <h3>Types of Marine Waste</h3>
                    <ul>
                        <li><strong>Plastic Debris:</strong> Accounts for 80% of all marine debris from surface waters to deep-sea sediments</li>
                        <li><strong>Fishing Gear:</strong> Abandoned nets, lines, and traps that continue to catch marine life</li>
                        <li><strong>Microplastics:</strong> Particles less than 5mm in size that can enter the food chain</li>
                        <li><strong>Chemical Pollutants:</strong> Including oil, industrial discharge, and agricultural runoff</li>
                    </ul>

                    <h3>Impact on Marine Ecosystems</h3>
                    <p>The effects of marine waste are far-reaching and often devastating:</p>
                    <ul>
                        <li>Marine animals become entangled in debris or mistake it for food</li>
                        <li>Coral reefs are damaged by waste accumulation and chemical pollution</li>
                        <li>Microplastics enter the food chain, affecting marine life and human health</li>
                        <li>Coastal ecosystems suffer from accumulated waste and habitat destruction</li>
                    </ul>

                    <h2>Solutions and Prevention</h2>
                    <p>Addressing marine waste requires a multi-faceted approach:</p>
                    <ol>
                        <li>Improved waste management systems</li>
                        <li>Reduction of single-use plastics</li>
                        <li>Enhanced recycling programs</li>
                        <li>Community education and engagement</li>
                        <li>International cooperation and policy implementation</li>
                    </ol>"
            ],
            [
                'category' => 'awareness',
                'title' => 'The Hidden Crisis: Microplastics in Our Oceans',
                'content' => "<h2>The Invisible Threat of Microplastics</h2>
                    <p>Microplastics have emerged as one of the most pervasive and concerning forms of marine pollution. These tiny plastic particles, measuring less than 5 millimeters, pose a significant threat to marine ecosystems and human health.</p>

                    <h3>Sources of Microplastics</h3>
                    <ul>
                        <li>Breakdown of larger plastic items</li>
                        <li>Microbeads from personal care products</li>
                        <li>Synthetic fibers from clothing</li>
                        <li>Industrial scrubbers and abrasives</li>
                    </ul>

                    <h3>Environmental Impact</h3>
                    <p>The presence of microplastics in our oceans has far-reaching consequences:</p>
                    <ul>
                        <li>Ingestion by marine organisms at all levels of the food chain</li>
                        <li>Accumulation of toxins and pollutants</li>
                        <li>Transfer of contaminants through the food web</li>
                        <li>Potential effects on human health through seafood consumption</li>
                    </ul>

                    <h2>Research and Monitoring</h2>
                    <p>Scientists are actively studying microplastic pollution through:</p>
                    <ul>
                        <li>Ocean sampling and monitoring programs</li>
                        <li>Analysis of marine organism tissue samples</li>
                        <li>Development of new detection methods</li>
                        <li>Assessment of environmental and health impacts</li>
                    </ul>"
            ],
            [
                'category' => 'best_practices',
                'title' => 'Effective Coastal Cleanup Strategies',
                'content' => "<h2>Organizing Successful Beach Cleanups</h2>
                    <p>Effective beach cleanup operations require careful planning, proper safety measures, and community engagement. This guide provides comprehensive information for organizing and conducting successful coastal cleanup events.</p>

                    <h3>Pre-Cleanup Planning</h3>
                    <ul>
                        <li>Site assessment and safety evaluation</li>
                        <li>Required permits and permissions</li>
                        <li>Equipment and supply preparation</li>
                        <li>Volunteer recruitment and coordination</li>
                    </ul>

                    <h3>During the Cleanup</h3>
                    <p>Essential procedures and safety measures:</p>
                    <ul>
                        <li>Proper waste sorting and collection techniques</li>
                        <li>Safety guidelines for volunteers</li>
                        <li>Documentation and data collection</li>
                        <li>First aid and emergency procedures</li>
                    </ul>

                    <h3>Post-Cleanup Activities</h3>
                    <ol>
                        <li>Proper disposal of collected waste</li>
                        <li>Data analysis and reporting</li>
                        <li>Follow-up with volunteers</li>
                        <li>Planning for future events</li>
                    </ol>"
            ]
        ];

        foreach ($articleTemplates as $template) {
            $article = Article::create([
                'title' => $template['title'],
                'content' => $template['content'],
                'category' => $template['category'],
                'author_id' => $authors->random()->id,
                'published_at' => Carbon::now()->subDays(rand(0, 30)),
                'is_featured' => rand(0, 3) === 0, // 33% chance of being featured
                'view_count' => rand(100, 5000),
                'metadata' => json_encode([
                    'reading_time' => rand(10, 25) . ' minutes',
                    'related_links' => [
                        'https://www.unep.org/marine-pollution',
                        'https://oceanservice.noaa.gov/facts/marinedebris.html',
                        'https://www.nationalgeographic.com/environment/article/ocean-pollution'
                    ],
                    'key_takeaways' => [
                        'Understanding marine waste types and sources',
                        'Impact on marine ecosystems',
                        'Prevention and mitigation strategies',
                        'Community involvement opportunities'
                    ]
                ])
            ]);

            // Attach 3-5 relevant tags to each article
            $article->tags()->attach(
                collect($tagModels)->random(rand(3, 5))->pluck('id')->toArray()
            );
        }
    }
} 