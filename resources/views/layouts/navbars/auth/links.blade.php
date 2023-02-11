<div class="flex flex-col lg:flex-row items-center justify-center space-y-4 lg:space-x-4 space-x-0 lg:space-y-0">
    <div class="nav-item">
        <a class="nav-link" href="{{ route('index') }}">{{ __('Wiki') }}</a>
    </div>
    @guest
        @if (Route::has('login'))
            <div class="nav-item">
                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
            </div>
        @endif

        @if (Route::has('register'))
            <div class="nav-item">
                <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
            </div>
        @endif
    @else
        <div class="nav-item">
            <a class="nav-link" href="{{ route('admin.index') }}">{{ __('Dashboard') }}</a>
        </div>
        <div class="nav-item dropdown">
            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                {{ Auth::user()->name }}
            </a>

            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                <a class="dropdown-item" href="{{ route('logout') }}"
                   onclick="event.preventDefault();
                                 document.getElementById('logout-form').submit();">
                    {{ __('Logout') }}
                </a>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>
        </div>
    @endguest
</div>
