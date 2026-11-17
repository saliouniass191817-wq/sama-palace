@extends('layouts.app')

@section('title', __('messages.ui.admin_users'))

@section('content')
<div class="container-storefront mx-auto">
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
        <div>
            <div class="section-kicker mb-1">{{ __('messages.ui.nav_admin') }}</div>
            <h1 class="h2 fw-bold mb-0">{{ __('messages.ui.admin_users') }}</h1>
        </div>
        <a class="btn btn-success btn-custom" href="{{ route('admin.users.create') }}">{{ __('messages.ui.create_account') }}</a>
    </div>

    <div class="app-card table-responsive p-3 p-md-4">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>{{ __('messages.ui.name') }}</th>
                    <th>{{ __('messages.ui.phone') }}</th>
                    <th>{{ __('messages.ui.role') }}</th>
                    <th>{{ __('messages.ui.shop_label') }}</th>
                    <th><span class="visually-hidden">{{ __('messages.ui.actions') }}</span></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr>
                        <td>{{ $user->firstName }} {{ $user->lastName }}</td>
                        <td>{{ $user->phone }}</td>
                        <td>{{ $user->isAdmin() ? __('messages.ui.administrator') : __('messages.ui.seller') }}</td>
                        <td>{{ $user->shop?->name ?? '—' }}</td>
                        <td class="text-end text-nowrap">
                            <a class="btn btn-sm btn-outline-primary btn-custom" href="{{ route('admin.users.edit', $user) }}">{{ __('messages.ui.edit') }}</a>
                            @unless ($user->is(auth()->user()) || ($user->isAdmin() && $adminCount <= 1))
                                <form class="d-inline" method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm(@js(__('messages.ui.delete_user_confirmation')))">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger btn-custom" type="submit">{{ __('messages.ui.remove') }}</button>
                                </form>
                            @endunless
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted-soft py-4">{{ __('messages.ui.no_accounts') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($users->hasPages())
        <div class="mt-4">{{ $users->links() }}</div>
    @endif
</div>
@endsection
