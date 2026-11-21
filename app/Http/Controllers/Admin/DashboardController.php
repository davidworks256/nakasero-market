<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\FarmerProfile;
use App\Models\Listing;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.overview', [
            'counts' => [
                'farmers' => User::where('role', 'farmer')->count(),
                'unverified' => FarmerProfile::whereNull('verified_at')->count(),
                'buyers' => User::where('role', 'buyer')->count(),
                'listings' => Listing::count(),
                'active_listings' => Listing::active()->count(),
                'orders' => Order::count(),
                'completed_orders' => Order::where('status', 'completed')->count(),
                'messages' => Conversation::count(),
            ],
            'pendingFarmers' => User::with('farmerProfile')
                ->whereHas('farmerProfile', fn ($q) => $q->whereNull('verified_at'))
                ->latest()
                ->take(10)
                ->get(),
        ]);
    }

    public function users(Request $request): View
    {
        $users = User::with('farmerProfile')
            ->when($request->input('role'), fn ($q, $role) => $q->where('role', $role))
            ->when($request->input('q'), fn ($q, $term) => $q->where(fn ($qq) => $qq
                ->where('name', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%")
                ->orWhere('phone', 'like', "%{$term}%")))
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.users', compact('users'));
    }

    public function verifyFarmer(User $user): RedirectResponse
    {
        abort_unless($user->isFarmer(), 404);

        $user->farmerProfile()->update(['verified_at' => now()]);

        return back()->with('status', "{$user->name} is now verified.");
    }

    public function suspend(User $user): RedirectResponse
    {
        abort_if($user->isAdmin(), 403);

        $user->update(['status' => 'suspended']);

        return back()->with('status', "{$user->name} has been suspended.");
    }

    public function activate(User $user): RedirectResponse
    {
        $user->update(['status' => 'active']);

        return back()->with('status', "{$user->name} is active again.");
    }

    public function listings(Request $request): View
    {
        $listings = Listing::with(['farmer', 'category'])
            ->when($request->input('status'), fn ($q, $s) => $q->where('status', $s))
            ->latest('updated_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.listings', compact('listings'));
    }

    public function hideListing(Listing $listing): RedirectResponse
    {
        $listing->update(['status' => Listing::STATUS_HIDDEN]);

        return back()->with('status', "Listing \"{$listing->title}\" is hidden from buyers.");
    }

    public function relistListing(Listing $listing): RedirectResponse
    {
        $listing->update(['status' => $listing->quantity_available > 0 ? Listing::STATUS_ACTIVE : Listing::STATUS_SOLD_OUT]);

        return back()->with('status', "Listing \"{$listing->title}\" is visible again.");
    }

    public function removeListing(Listing $listing): RedirectResponse
    {
        $listing->delete();

        return back()->with('status', 'Listing removed.');
    }
}
