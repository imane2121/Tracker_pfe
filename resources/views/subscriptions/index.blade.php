@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-2xl font-bold mb-6 text-gray-800">Region Subscriptions</h2>

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <!-- Add New Subscription -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold mb-4 text-gray-700">Subscribe to a New Region</h3>
                <form action="{{ route('subscriptions.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="region" class="block text-sm font-medium text-gray-700">Region</label>
                            <select name="region" id="region" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Select a region</option>
                                @foreach($availableRegions as $region)
                                    @if(!in_array($region, $subscribedRegions))
                                        <option value="{{ $region }}">{{ $region }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-2">
                            <div class="flex items-center">
                                <input type="checkbox" name="email_notifications" id="email_notifications" value="1" checked class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <label for="email_notifications" class="ml-2 block text-sm text-gray-700">Email Notifications</label>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" name="push_notifications" id="push_notifications" value="1" checked class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <label for="push_notifications" class="ml-2 block text-sm text-gray-700">Push Notifications</label>
                            </div>
                        </div>
                    </div>
                    <div>
                        <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Subscribe
                        </button>
                    </div>
                </form>
            </div>

            <!-- Current Subscriptions -->
            <div>
                <h3 class="text-lg font-semibold mb-4 text-gray-700">Your Current Subscriptions</h3>
                @if($subscriptions->isEmpty())
                    <p class="text-gray-500">You haven't subscribed to any regions yet.</p>
                @else
                    <div class="space-y-4">
                        @foreach($subscriptions as $subscription)
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h4 class="text-lg font-medium text-gray-900">{{ $subscription->region }}</h4>
                                        <p class="text-sm text-gray-500">Subscribed on {{ $subscription->created_at->format('M d, Y') }}</p>
                                    </div>
                                    <form action="{{ route('subscriptions.destroy', $subscription->region) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('Are you sure you want to unsubscribe from this region?')">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                                <form action="{{ route('subscriptions.update', $subscription->region) }}" method="POST" class="mt-4">
                                    @csrf
                                    @method('PUT')
                                    <div class="flex items-center space-x-4">
                                        <div class="flex items-center">
                                            <input type="checkbox" name="email_notifications" id="email_notifications_{{ $subscription->id }}" value="1" {{ $subscription->email_notifications ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <label for="email_notifications_{{ $subscription->id }}" class="ml-2 block text-sm text-gray-700">Email Notifications</label>
                                        </div>
                                        <div class="flex items-center">
                                            <input type="checkbox" name="push_notifications" id="push_notifications_{{ $subscription->id }}" value="1" {{ $subscription->push_notifications ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <label for="push_notifications_{{ $subscription->id }}" class="ml-2 block text-sm text-gray-700">Push Notifications</label>
                                        </div>
                                        <button type="submit" class="text-sm text-indigo-600 hover:text-indigo-800">Update Preferences</button>
                                    </div>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 