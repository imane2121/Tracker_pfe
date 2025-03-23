<?php

namespace App\Http\Controllers;

use App\Models\RegionSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RegionSubscriptionController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $subscriptions = $user->regionSubscriptions;
        $availableRegions = RegionSubscription::getAvailableRegions();
        $subscribedRegions = $subscriptions->pluck('region')->toArray();

        return view('subscriptions.index', compact('subscriptions', 'availableRegions', 'subscribedRegions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'region' => 'required|string|in:' . implode(',', RegionSubscription::getAvailableRegions()),
            'email_notifications' => 'boolean',
            'push_notifications' => 'boolean',
        ]);

        $user = Auth::user();

        if ($user->isSubscribedToRegion($request->region)) {
            return back()->with('error', 'You are already subscribed to this region.');
        }

        $user->subscribeToRegion(
            $request->region,
            $request->boolean('email_notifications', true),
            $request->boolean('push_notifications', true)
        );

        return back()->with('success', 'Successfully subscribed to ' . $request->region);
    }

    public function destroy(string $region)
    {
        $user = Auth::user();

        if (!$user->isSubscribedToRegion($region)) {
            return back()->with('error', 'You are not subscribed to this region.');
        }

        $user->unsubscribeFromRegion($region);

        return back()->with('success', 'Successfully unsubscribed from ' . $region);
    }

    public function update(Request $request, string $region)
    {
        $request->validate([
            'email_notifications' => 'boolean',
            'push_notifications' => 'boolean',
        ]);

        $user = Auth::user();
        $subscription = $user->regionSubscriptions()->where('region', $region)->first();

        if (!$subscription) {
            return back()->with('error', 'Subscription not found.');
        }

        $subscription->update([
            'email_notifications' => $request->boolean('email_notifications', true),
            'push_notifications' => $request->boolean('push_notifications', true),
        ]);

        return back()->with('success', 'Subscription preferences updated successfully.');
    }
} 