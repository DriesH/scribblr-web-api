@extends('layouts.app')

@section('content')
<div class="register-container theme-1-maincolor">

    <div class="register-container__logo">
        <span class="scribblr-logo">Scribblr</span>
    </div>

    <div class="register-container__heading">
        <h1 id="hello-there" class="big-header-text theme-1-maincolor">Final step.</h1>
        <h2 id="new-face" class="sub-header-text theme-1-subcolor">Some additional data we will need.</h2>
    </div>

    <form class="register-container__form" action="{{ route('register') }}" method="POST">
        {{ csrf_field() }}
        <div class="register-form__input-field-container">
            <div class="register-form-input-field">
                <label for="first_name">First name</label>
                <input id="first_name" type="text" class="form-control" name="first_name" value="{{ old('first_name') }}" required autofocus>
            </div>

            @if ($errors->has('first_name'))
                {{ $errors->first('first_name') }}
            @endif

            <div class="register-form-input-field">
                <label for="last_name">Last name</label>
                <input id="last_name" type="text" class="form-control" name="last_name" value="{{ old('last_name') }}" required autofocus>
            </div>

            @if ($errors->has('last_name'))
                {{ $errors->first('last_name') }}
            @endif

            <div class="register-form-input-field">
                <label for="password">Password</label>
                <input id="password" type="password" class="form-control" name="password" required>
            </div>

            @if ($errors->has('password'))
                {{ $errors->first('password') }}
            @endif

            <button class="btn btn-submit theme-1-subcolor theme-1-maincolor-text" type="submit">Save account information</button>
        </div>
    </form>

</div>
@endsection
