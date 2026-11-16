<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ManageUserRequest;
use App\Models\User;
use App\Support\PublicMedia;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class AdminUserController extends Controller
{
    public function index(): View
    {
        return view('admin.users.index', [
            'users' => User::with('shop')->latest()->paginate(20),
        ]);
    }

    public function create(): View
    {
        return view('admin.users.create');
    }

    public function store(ManageUserRequest $request): RedirectResponse
    {
        User::create($request->validated());

        return redirect()->route('admin.users.index')->with('success', 'Le compte a été créé.');
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(ManageUserRequest $request, User $user): RedirectResponse
    {
        $validated = $request->validated();

        if (blank($validated['password'] ?? null)) {
            unset($validated['password']);
        }

        if (! $user->update($validated)) {
            throw new RuntimeException('Le compte n’a pas pu être mis à jour.');
        }

        return redirect()->route('admin.users.index')->with('success', 'Le compte a été mis à jour.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        abort_if($user->is($request->user()), 403);
        abort_if($user->isAdmin() && User::where('role', 'admin')->count() <= 1, 403);

        $shop = $user->shop()->with('products')->first();
        $productImages = $shop?->products->pluck('image') ?? collect();
        $shopLogo = $shop?->logo;

        if (! $user->delete()) {
            throw new RuntimeException('Le compte n’a pas pu être supprimé.');
        }

        foreach ($productImages as $image) {
            PublicMedia::delete($image);
        }

        PublicMedia::delete($shopLogo);

        return redirect()->route('admin.users.index')->with('success', 'Le compte a été supprimé.');
    }
}
