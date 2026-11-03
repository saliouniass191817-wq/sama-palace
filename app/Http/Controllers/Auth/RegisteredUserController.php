<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(RegisterUserRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $user = User::create($validated);

        Auth::login($user);

        return redirect()
            ->route('shops.create')
            ->with('success', 'Inscription confirmee pour le numero '.$user->phone.'. Vous pouvez maintenant creer votre boutique.');
    }
}
