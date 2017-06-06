@extends('layouts.app')

@section('content')
    <div class="register-container theme-1-maincolor">

        <div class="register-container__logo">
            <span class="scribblr-logo">Scribblr</span>
        </div>

        <div class="register-container__heading">
            <h1 id="hello-there" class="big-header-text theme-1-maincolor">Hello there.</h1>
            <h2 id="new-face" class="sub-header-text theme-1-subcolor">Let's put a name to the new face!</h2>
        </div>

        <form id="register_form" class="register-container__form" action="{{ route('register') }}" method="POST">
            {{ csrf_field() }}
            <div class="register-form__input-field-container">
                <div class="register-form-input-field">
                    <label for="fullname">Fullname</label>
                    <input id="fullname" type="text" class="form-control" name="fullname" value="{{ old('fullname') }}" required autofocus>
                </div>

                @if ($errors->has('fullname'))
                    {{ $errors->first('fullname') }}
                @endif

                <div class="register-form-input-field">
                    <label for="email">Email</label>
                    <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>
                </div>

                @if ($errors->has('email'))
                    {{ $errors->first('email') }}
                @endif

                <div class="register-form-input-field">
                    <label for="password">Password</label>
                    <input id="password" type="password" class="form-control" name="password" required>
                </div>

                @if ($errors->has('password'))
                    {{ $errors->first('password') }}
                @endif

                <button class="btn btn-submit theme-1-subcolor theme-1-maincolor-text" type="submit">Register</button>
            </div>
        </form>

    </div>
@endsection

@section('scripts')
    <script type="text/javascript">
        $(function() {
            // form
            var form$ = $('#register_form');

            // submit form
            form$.submit(function(e) {
                e.preventDefault();
                register();
            });

            // register user
            function register() {
                $.ajax({
                    type: 'POST',
                    data: form$.serializeArray(),
                    dataType: 'json',
                    success: getUser,
                    error: function (error) {
                        console.log(error, 'Vies hard geerrordddddd!');
                    }
                });
            }

            // get user data
            function getUser(data) {
                $.ajax({
                    type: 'GET',
                    url: '/api/user',
                    dataType: 'json',
                    headers: {
                        'Authorization': 'Bearer ' + data.token
                    },
                    success: function(successData) {
                        console.log(successData);
                    }
                });
            }
        });
    </script>
@endsection
