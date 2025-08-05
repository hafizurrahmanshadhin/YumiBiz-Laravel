<?php

namespace Database\Seeders;

use App\Models\Subscription;
use Illuminate\Database\Seeder;

class SubscriptionSeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void {
        $subscriptions = [
            [
                'package_type'  => 'premium',
                'timeline'      => '12',
                'price'         => '149.99',
                'feature'       => json_encode([
                    '50 Swipes per day',
                    '20 Rewinds per day',
                    'No Ads',
                    'Travel Mode',
                    'Group Chat',
                    '1 Free Boost per month',
                    'Advanced Filter',
                    'Read Receipt',
                    'Pin Chat',
                    'Special Badge',
                ]),
                'rewinds_limit' => '20',
                'swipes_limit'  => '50',
            ],
            [
                'package_type'  => 'premium',
                'timeline'      => '6',
                'price'         => '49.99',
                'feature'       => json_encode([
                    '50 Swipes per day',
                    '20 Rewinds per day',
                    'No Ads',
                    'Travel Mode',
                    'Group Chat',
                    '1 Free Boost per month',
                    'Advanced Filter',
                    'Read Receipt',
                    'Pin Chat',
                    'Special Badge',
                ]),
                'rewinds_limit' => '20',
                'swipes_limit'  => '50',
            ],
            [
                'package_type'  => 'premium',
                'timeline'      => '1',
                'price'         => '24.99',
                'feature'       => json_encode([
                    '50 Swipes per day',
                    '20 Rewinds per day',
                    'No Ads',
                    'Travel Mode',
                    'Group Chat',
                    '1 Free Boost per month',
                    'Advanced Filter',
                    'Read Receipt',
                    'Pin Chat',
                    'Special Badge',
                ]),
                'rewinds_limit' => '20',
                'swipes_limit'  => '50',
            ],
            [
                'package_type'  => 'prestige',
                'timeline'      => '12',
                'price'         => '149.99',
                'feature'       => json_encode([
                    'Unlimited Swipes',
                    'Unlimited Rewinds',
                    'No Ads',
                    'Travel Mode',
                    'Group Chat',
                    '3 Free Boost per month',
                    'Advanced Filters',
                    'Read Receipt',
                    'Pin Chat',
                    'Special Badge',
                    'Unsend Messages',
                    'Edit Messages',
                    'See Who Likes You',
                    'Message Before Match',
                    'Typing Status',
                    'Unlimited Messages',
                    'Chat Translate',
                ]),
                'rewinds_limit' => '1000000000',
                'swipes_limit'  => '1000000000',
            ],
            [
                'package_type'  => 'prestige',
                'timeline'      => '6',
                'price'         => '49.99',
                'feature'       => json_encode([
                    'Unlimited Swipes',
                    'Unlimited Rewinds',
                    'No Ads',
                    'Travel Mode',
                    'Group Chat',
                    '3 Free Boost per month',
                    'Advanced Filters',
                    'Read Receipt',
                    'Pin Chat',
                    'Special Badge',
                    'Unsend Messages',
                    'Edit Messages',
                    'See Who Likes You',
                    'Message Before Match',
                    'Typing Status',
                    'Unlimited Messages',
                    'Chat Translate',
                ]),
                'rewinds_limit' => '1000000000',
                'swipes_limit'  => '1000000000',
            ],
            [
                'package_type'  => 'prestige',
                'timeline'      => '1',
                'price'         => '24.99',
                'feature'       => json_encode([
                    'Unlimited Swipes',
                    'Unlimited Rewinds',
                    'No Ads',
                    'Travel Mode',
                    'Group Chat',
                    '3 Free Boost per month',
                    'Advanced Filters',
                    'Read Receipt',
                    'Pin Chat',
                    'Special Badge',
                    'Unsend Messages',
                    'Edit Messages',
                    'See Who Likes You',
                    'Message Before Match',
                    'Typing Status',
                    'Unlimited Messages',
                    'Chat Translate',
                ]),
                'rewinds_limit' => '1000000000',
                'swipes_limit'  => '1000000000',
            ],
        ];
        foreach ($subscriptions as $subscription) {
            Subscription::create($subscription);
        }
    }
}
