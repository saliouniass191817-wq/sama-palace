@php($editing = isset($user))

<form method="POST" action="{{ $editing ? route('admin.users.update', $user) : route('admin.users.store') }}" class="app-card p-3 p-md-4">
    @csrf
    @if ($editing)
        @method('PUT')
    @endif

    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label" for="firstName">{{ __('messages.ui.first_name') }}</label>
            <input class="form-control" id="firstName" name="firstName" value="{{ old('firstName', $user->firstName ?? '') }}" required>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="lastName">{{ __('messages.ui.name') }}</label>
            <input class="form-control" id="lastName" name="lastName" value="{{ old('lastName', $user->lastName ?? '') }}" required>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="phone">{{ __('messages.ui.phone') }}</label>
            <input class="form-control" id="phone" name="phone" value="{{ old('phone', $user->phone ?? '') }}" placeholder="+221771234567" required>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="adress">{{ __('messages.ui.address') }}</label>
            <input class="form-control" id="adress" name="adress" value="{{ old('adress', $user->adress ?? '') }}" required>
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
