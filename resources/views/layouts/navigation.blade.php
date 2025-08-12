@auth
    <nav x-data="{ open: false }"
        class="bg-white/95 backdrop-blur-md shadow-lg border-b-2 border-red-500/20 sticky top-0 z-50 transition-all duration-300">
        <!-- Primary Navigation Menu -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 lg:h-20">
                <div class="flex items-center flex-1 min-w-0">
                    <!-- Logo -->
                    <div class="flex-shrink-0">
                        <a href="{{ route('incidents.index') }}" class="flex items-center space-x-2 lg:space-x-3">
                            <div
                                class="w-8 h-8 lg:w-10 lg:h-10 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center shadow-lg transform hover:scale-105 transition-transform duration-200">
                                <svg class="w-4 h-4 lg:w-6 lg:h-6 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z">
                                    </path>
                                </svg>
                            </div>
                            <div class="hidden sm:block min-w-0">
                                <div class="flex items-center space-x-2">
                                    <div
                                        class="text-lg lg:text-xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 bg-clip-text text-transparent truncate">
                                        Incident Management</div>
                                    <span class="text-xs font-mono text-gray-400 bg-gray-100 px-2 py-1 rounded-md">v1.002</span>
                                </div>
                                <div
                                    class="text-xs lg:text-sm font-medium bg-gradient-to-r from-red-500 to-red-600 bg-clip-text text-transparent">
                                    Professional System</div>
                            </div>
                            <div
                                class="sm:hidden text-base font-bold bg-gradient-to-r from-gray-900 to-gray-700 bg-clip-text text-transparent">
                                IMS</div>
                        </a>
                    </div>

                    <!-- Navigation Links -->
                    <div class="hidden md:flex md:ml-6 lg:ml-8 space-x-2">
                        <a href="{{ route('incidents.index') }}"
                            class="inline-flex items-center px-3 lg:px-4 py-2 rounded-xl text-sm font-medium transition-all duration-300 transform hover:scale-105 {{ request()->routeIs('incidents.*') ? 'bg-gradient-to-r from-red-50 to-red-100 text-red-700 shadow-sm border border-red-200/50' : 'text-gray-600 hover:text-red-600 hover:bg-gradient-to-r hover:from-red-50 hover:to-red-100' }}">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            <span class="hidden lg:inline">{{ __('Incidents Dashboard') }}</span>
                            <span class="lg:hidden">{{ __('Dashboard') }}</span>
                        </a>
                        
                        <a href="{{ route('logs.index') }}"
                            class="inline-flex items-center px-3 lg:px-4 py-2 rounded-xl text-sm font-medium transition-all duration-300 transform hover:scale-105 {{ request()->routeIs('logs.*') ? 'bg-gradient-to-r from-blue-50 to-blue-100 text-blue-700 shadow-sm border border-blue-200/50' : 'text-gray-600 hover:text-blue-600 hover:bg-gradient-to-r hover:from-blue-50 hover:to-blue-100' }}">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span class="hidden lg:inline">{{ __('All Logs') }}</span>
                            <span class="lg:hidden">{{ __('Logs') }}</span>
                        </a>
                    </div>
                </div>

                <!-- User Menu -->
                <div class="flex items-center space-x-2 lg:space-x-4 flex-shrink-0">
                    <!-- User Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <!-- User Avatar/Name Button -->
                        <button @click="open = !open"
                            class="flex items-center space-x-3 px-2 py-2 rounded-xl hover:bg-gradient-to-r hover:from-red-50 hover:to-red-100 focus:outline-none focus:ring-2 focus:ring-red-500/30 focus:ring-offset-2 transition-all duration-300 group">
                            <!-- User Info (Hidden on small screens) -->
                            <div class="hidden lg:block text-right">
                                <div
                                    class="text-sm font-medium text-gray-900 truncate max-w-32 group-hover:text-red-600 transition-colors duration-200">
                                    {{ Auth::user()->name }}
                                </div>
                                <div class="text-xs text-gray-500 group-hover:text-red-500 transition-colors duration-200">
                                    {{ Auth::user()->getRoleDisplayName() }}</div>
                            </div>
                            <!-- Avatar -->
                            <div
                                class="w-8 h-8 lg:w-9 lg:h-9 bg-gradient-to-br from-red-500 to-red-600 rounded-full flex items-center justify-center shadow-md transform group-hover:scale-110 transition-all duration-200">
                                <span class="text-white text-sm font-medium">{{ substr(Auth::user()->name, 0, 1) }}</span>
                            </div>
                            <!-- Dropdown Arrow -->
                            <svg class="w-4 h-4 text-gray-400 group-hover:text-red-500 transform transition-all duration-200"
                                :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                                </path>
                            </svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="transform opacity-0 scale-95 -translate-y-1"
                            x-transition:enter-end="transform opacity-100 scale-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="transform opacity-100 scale-100 translate-y-0"
                            x-transition:leave-end="transform opacity-0 scale-95 -translate-y-1"
                            class="absolute right-0 z-[9999] mt-2 w-72 rounded-2xl shadow-2xl bg-white/95 backdrop-blur-md ring-1 ring-gray-200/50 border border-gray-100/50"
                            style="display: none;">
                            <div class="py-3">
                                <!-- User Header -->
                                <div
                                    class="px-5 py-4 border-b border-gray-100/50 bg-gradient-to-r from-gray-50/80 to-red-50/30 rounded-t-2xl">
                                    <div class="flex items-center space-x-3">
                                        <div
                                            class="w-10 h-10 bg-gradient-to-br from-red-500 to-red-600 rounded-full flex items-center justify-center shadow-lg">
                                            <span
                                                class="text-white font-medium">{{ substr(Auth::user()->name, 0, 1) }}</span>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm font-semibold text-gray-900 truncate">{{ Auth::user()->name }}
                                            </p>
                                            <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
                                            <p class="text-xs font-medium 
                                                @if(Auth::user()->isAdmin()) 
                                                    text-red-500
                                                @elseif(Auth::user()->isEditor()) 
                                                    text-blue-500
                                                @else 
                                                    text-green-500
                                                @endif">
                                                {{ Auth::user()->getRoleDisplayName() }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Menu Items -->
                                <div class="py-2">
                                    <a href="#"
                                        class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gradient-to-r hover:from-red-50 hover:to-red-100 hover:text-red-600 transition-all duration-300 rounded-lg mx-2">
                                        <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                            </path>
                                        </svg>
                                        <span class="font-medium">Profile Settings</span>
                                    </a>

                                    @if(Auth::user()->canEditIncidents())
                                        <a href="{{ route('incidents.create') }}"
                                            class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gradient-to-r hover:from-red-50 hover:to-red-100 hover:text-red-600 transition-all duration-300 rounded-lg mx-2">
                                            <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                            <span class="font-medium">New Incident</span>
                                        </a>
                                    @endif

                                    <div class="my-2 border-t border-gray-100/50"></div>

                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit"
                                            class="flex items-center w-full px-4 py-3 text-sm text-gray-700 hover:bg-gradient-to-r hover:from-red-50 hover:to-red-100 hover:text-red-600 transition-all duration-300 rounded-lg mx-2">
                                            <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                                </path>
                                            </svg>
                                            <span class="font-medium">Sign Out</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Mobile menu button -->
                    <div class="md:hidden">
                        <button @click="open = ! open"
                            class="inline-flex items-center justify-center p-2 rounded-xl text-gray-400 hover:text-red-600 hover:bg-gradient-to-r hover:from-red-50 hover:to-red-100 focus:outline-none focus:ring-2 focus:ring-red-500/30 focus:ring-offset-2 transition-all duration-300 transform hover:scale-110">
                            <svg class="h-5 w-5" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex"
                                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16" />
                                <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden"
                                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Navigation Menu -->
        <div :class="{'block': open, 'hidden': ! open}"
            class="hidden md:hidden bg-white/95 backdrop-blur-md border-t border-gray-200/50">
            <!-- User Info Mobile -->
            <div class="px-4 py-4 border-b border-gray-200/50 bg-gradient-to-r from-gray-50/80 to-red-50/60">
                <div class="flex items-center space-x-3">
                    <div
                        class="w-10 h-10 bg-gradient-to-br from-red-500 to-red-600 rounded-full flex items-center justify-center shadow-lg">
                        <span class="text-white font-medium">{{ substr(Auth::user()->name, 0, 1) }}</span>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="font-semibold text-gray-900 truncate">{{ Auth::user()->name }}</div>
                        <div class="text-sm text-gray-600 truncate">{{ Auth::user()->email }}</div>
                        <div class="text-xs font-medium 
                            @if(Auth::user()->isAdmin()) 
                                text-red-500
                            @elseif(Auth::user()->isEditor()) 
                                text-blue-500
                            @else 
                                text-green-500
                            @endif">
                            {{ Auth::user()->getRoleDisplayName() }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigation Links Mobile -->
            <div class="px-4 py-4 space-y-2">
                <a href="{{ route('incidents.index') }}"
                    class="flex items-center px-4 py-3 rounded-xl text-base font-medium transition-all duration-300 transform hover:scale-105 {{ request()->routeIs('incidents.*') ? 'bg-gradient-to-r from-red-50 to-red-100 text-red-700 shadow-md border border-red-200/50' : 'text-gray-700 hover:text-red-600 hover:bg-gradient-to-r hover:from-red-50 hover:to-red-100' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    <span>Incidents Dashboard</span>
                </a>
                
                <a href="{{ route('logs.index') }}"
                    class="flex items-center px-4 py-3 rounded-xl text-base font-medium transition-all duration-300 transform hover:scale-105 {{ request()->routeIs('logs.*') ? 'bg-gradient-to-r from-blue-50 to-blue-100 text-blue-700 shadow-md border border-blue-200/50' : 'text-gray-700 hover:text-blue-600 hover:bg-gradient-to-r hover:from-blue-50 hover:to-blue-100' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>All Logs</span>
                </a>

                <a href="#"
                    class="flex items-center px-4 py-3 rounded-xl text-base font-medium text-gray-700 hover:text-red-600 hover:bg-gradient-to-r hover:from-red-50 hover:to-red-100 transition-all duration-300 transform hover:scale-105">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span>Profile Settings</span>
                </a>

                <form method="POST" action="{{ route('logout') }}" class="mt-4 pt-4 border-t border-gray-200">
                    @csrf
                    <button type="submit"
                        class="flex items-center w-full px-4 py-3 rounded-xl text-base font-medium text-gray-700 hover:text-red-600 hover:bg-gradient-to-r hover:from-red-50 hover:to-red-100 transition-all duration-300 transform hover:scale-105">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                            </path>
                        </svg>
                        <span>Sign Out</span>
                    </button>
                </form>
            </div>
        </div>
    </nav>
@endauth