<nav x-data="{ mobileOpen: false, userMenu: false }"
     style="background:#fff; border-bottom:1px solid var(--border); position:sticky; top:0; z-index:50;">
    <div class="max-w-6xl mx-auto px-4 sm:px-6">
        <div style="display:flex; align-items:center; justify-content:space-between; height:60px;">

            {{-- Logo --}}
            <a href="{{ route('home') }}" style="display:flex; align-items:center; gap:10px; text-decoration:none;">
                <div style="width:34px; height:34px; background:var(--teal); border-radius:9px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <i class="fas fa-heart-pulse" style="color:#fff; font-size:14px;"></i>
                </div>
                <span style="font-size:16px; font-weight:600; color:var(--ink);">Health<span style="color:var(--teal);">360</span></span>
            </a>

            {{-- Desktop links --}}
            <div class="hidden md:flex items-center gap-1">
                @auth
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-grid-2 text-xs"></i> Dashboard
                </a>
                <a href="{{ route('symptoms.index') }}" class="nav-link {{ request()->routeIs('symptoms.index') ? 'active' : '' }}">
                    <i class="fas fa-stethoscope text-xs"></i> Symptom Check
                </a>
                <a href="{{ route('symptoms.history') }}" class="nav-link {{ request()->routeIs('symptoms.history') ? 'active' : '' }}">
                    <i class="fas fa-clock-rotate-left text-xs"></i> History
                </a>
                @if(auth()->user() && auth()->user()->isDoctor())
                    <a href="{{ route('doctor.dashboard') }}" class="nav-link {{ request()->routeIs('doctor.*') ? 'active' : '' }}">
                        <i class="fas fa-user-doctor text-xs"></i> Doctor Portal
                    </a>
                @endif
                @else
                <a href="{{ route('login') }}" class="nav-link">
                    <i class="fas fa-sign-in-alt text-xs"></i> Sign In
                </a>
                <a href="{{ route('register') }}" class="nav-link">
                    <i class="fas fa-user-plus text-xs"></i> Register
                </a>
                @endauth
            </div>

            {{-- Right side --}}
            <div style="display:flex; align-items:center; gap:8px;">
                @auth
                {{-- Notification bell --}}
                <button style="position:relative; width:36px; height:36px; border-radius:8px; border:1px solid var(--border); background:#fff; cursor:pointer; display:flex; align-items:center; justify-content:center; color:var(--muted);">
                    <i class="fas fa-bell" style="font-size:14px;"></i>
                    <span style="position:absolute; top:7px; right:7px; width:7px; height:7px; background:var(--rose); border-radius:50%; border:1.5px solid #fff;"></span>
                </button>

                {{-- User menu --}}
                <div style="position:relative;">
                    <button @click="userMenu = !userMenu"
                            style="display:flex; align-items:center; gap:8px; padding:5px 10px 5px 5px; border-radius:9px; border:1px solid var(--border); background:#fff; cursor:pointer;">
                        <div style="width:30px; height:30px; border-radius:7px; background:linear-gradient(135deg,#0a9688,#4db6ac); display:flex; align-items:center; justify-content:center; color:#fff; font-size:12px; font-weight:600;">
                            {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                        </div>
                        <div style="text-align:left; line-height:1.2;" class="hidden sm:block">
                            <div style="font-size:13px; font-weight:500; color:var(--ink);">{{ auth()->user()->name }}</div>
                            <div style="font-size:11px; color:var(--muted);">{{ auth()->user() && auth()->user()->isDoctor() ? 'Physician' : 'Patient' }}</div>
                        </div>
                        <i class="fas fa-chevron-down" style="font-size:10px; color:var(--muted);"></i>
                    </button>

                    <div x-show="userMenu" @click.outside="userMenu = false" x-cloak
                         style="position:absolute; right:0; top:calc(100% + 6px); width:200px; background:#fff; border:1px solid var(--border); border-radius:var(--radius); overflow:hidden; box-shadow:0 8px 24px rgba(0,0,0,.1);">
                        <a href="{{ route('profile.edit') }}" style="display:flex; align-items:center; gap:9px; padding:10px 14px; font-size:13px; color:var(--ink); transition:background .1s;"
                           onmouseover="this.style.background='var(--surface)'" onmouseout="this.style.background='transparent'">
                            <i class="fas fa-user" style="font-size:13px; color:var(--muted); width:14px;"></i> Profile
                        </a>
                        <a href="#" style="display:flex; align-items:center; gap:9px; padding:10px 14px; font-size:13px; color:var(--ink); transition:background .1s;"
                           onmouseover="this.style.background='var(--surface)'" onmouseout="this.style.background='transparent'">
                            <i class="fas fa-gear" style="font-size:13px; color:var(--muted); width:14px;"></i> Settings
                        </a>
                        <hr style="border:none; border-top:1px solid var(--border); margin:4px 0;">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" style="width:100%; display:flex; align-items:center; gap:9px; padding:10px 14px; font-size:13px; color:var(--rose); background:transparent; border:none; cursor:pointer; text-align:left;"
                                    onmouseover="this.style.background='var(--rose-light)'" onmouseout="this.style.background='transparent'">
                                <i class="fas fa-arrow-right-from-bracket" style="width:14px;"></i> Sign out
                            </button>
                        </form>
                    </div>
                </div>
                @else
                {{-- Guest buttons --}}
                <a href="{{ route('login') }}" class="btn btn-outline btn-sm" style="display:inline-flex; align-items:center; gap:6px;">
                    <i class="fas fa-sign-in-alt"></i> Sign In
                </a>
                <a href="{{ route('register') }}" class="btn btn-primary btn-sm" style="display:inline-flex; align-items:center; gap:6px;">
                    <i class="fas fa-user-plus"></i> Register
                </a>
                @endauth

                {{-- Mobile hamburger --}}
                <button @click="mobileOpen = !mobileOpen" class="md:hidden"
                        style="width:36px; height:36px; border-radius:8px; border:1px solid var(--border); background:#fff; cursor:pointer; display:flex; align-items:center; justify-content:center; color:var(--muted);">
                    <i class="fas" :class="mobileOpen ? 'fa-xmark' : 'fa-bars'" style="font-size:14px;"></i>
                </button>
            </div>
        </div>

        {{-- Mobile menu --}}
        <div x-show="mobileOpen" x-cloak style="padding-bottom:12px; display:flex; flex-direction:column; gap:2px; max-height:70vh; overflow-y:auto;" class="md:hidden">
            @auth
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fas fa-grid-2 text-xs"></i> Dashboard
            </a>
            <a href="{{ route('symptoms.index') }}" class="nav-link {{ request()->routeIs('symptoms.index') ? 'active' : '' }}">
                <i class="fas fa-stethoscope text-xs"></i> Symptom Check
            </a>
            <a href="{{ route('symptoms.history') }}" class="nav-link {{ request()->routeIs('symptoms.history') ? 'active' : '' }}">
                <i class="fas fa-clock-rotate-left text-xs"></i> History
            </a>
            @if(auth()->user() && auth()->user()->isDoctor())
                <a href="{{ route('doctor.dashboard') }}" class="nav-link {{ request()->routeIs('doctor.*') ? 'active' : '' }}">
                    <i class="fas fa-user-doctor text-xs"></i> Doctor Portal
                </a>
            @endif
            @else
            <a href="{{ route('login') }}" class="nav-link">
                <i class="fas fa-sign-in-alt text-xs"></i> Sign In
            </a>
            <a href="{{ route('register') }}" class="nav-link">
                <i class="fas fa-user-plus text-xs"></i> Register
            </a>
            @endauth
        </div>
    </div>
</nav>
