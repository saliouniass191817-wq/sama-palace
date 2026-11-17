@php($editing = isset($user))

<form method="POST" action="{{ $editing ? route('admin.users.update', $user) : route('admin.users.store') }}" class="app-card p-3 p-md-4">
    @csrf
    @if ($editing)
        @method('PUT')
    @endif

    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label" for="first_name">{{ __('messages.ui.first_name') }}</label>
            <input class="form-control" id="first_name" name="first_name" value="{{ old('first_name', $user->first_name ?? '') }}" required>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="last_name">{{ __('messages.ui.name') }}</label>
            <input class="form-control" id="last_name" name="last_name" value="{{ old('last_name', $user->last_name ?? '') }}" required>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="phone">{{ __('messages.ui.phone') }}</label>
            <input class="form-control" id="phone" name="phone" value="{{ old('phone', $user->phone ?? '') }}" placeholder="+221771234567" required>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="address">{{ __('messages.ui.address') }}</label>
            <input class="form-control" id="address" name="address" value="{{ old('address', $user->address ?? '') }}" required>
        </div>
        <div class="col-12">
            <label class="form-label" for="password">{{ $editing ? __('messages.ui.new_password_optional') : __('messages.ui.password') }}</label>
            <input class="form-control" id="password" type="password" name="password" {{ $editing ? '' : 'required' }} autocomplete="new-password">
        </div>
    </div>

    <div class="d-flex gap-2 mt-4">
        <button class="btn btn-success btn-custom" type="submit">{{ $editing ? __('messages.ui.save') : __('messages.ui.create_account_button') }}</button>
        <a class="btn btn-outline-primary btn-custom" href="{{ route('admin.users.index') }}">{{ __('messages.ui.cancel') }}</a>
    </div>
</form>
