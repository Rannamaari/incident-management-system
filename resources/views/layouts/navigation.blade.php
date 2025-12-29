@auth
    <nav x-data="{ open: false }"
        class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-50">
        <!-- Primary Navigation Menu -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-14">
                <div class="flex items-center min-w-0 flex-shrink">
                    <!-- Logo -->
                    <div class="flex-shrink-0">
                        <a href="{{ route('home') }}" class="flex items-center space-x-2">
                            <div class="w-8 h-8 bg-red-600 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z">
                                    </path>
                                </svg>
                            </div>
                            <div class="hidden sm:flex items-baseline space-x-2">
                                <span class="text-sm font-semibold text-gray-900">Incident Management System</span>
                                <span class="text-xs text-gray-500">v3.6</span>
                            </div>
                            <span class="sm:hidden text-sm font-semibold text-gray-900">IMS</span>
                        </a>
                    </div>

                    <!-- Navigation Links -->
                    <div class="hidden lg:flex lg:ml-8 space-x-1">
                        <a href="{{ route('incidents.index') }}"
                            class="inline-flex items-center px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('incidents.*') ? 'text-red-600 border-b-2 border-red-600' : 'text-gray-600 hover:text-gray-900 border-b-2 border-transparent' }}">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-1.964-1.333-2.732 0L3.732 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <span>Incidents</span>
                        </a>

                        <a href="{{ route('logs.index') }}"
                            class="inline-flex items-center px-3 py-2 text-sm font-medium transition-colors relative {{ request()->routeIs('logs.*') ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-600 hover:text-gray-900 border-b-2 border-transparent' }}">
                            @php
                                $unreadCount = \App\Models\Incident::whereNotNull('timeline')
                                    ->get()
                                    ->filter(function($incident) {
                                        return $incident->hasUnreadTimelineUpdates();
                                    })
                                    ->count();
                            @endphp
                            @if($unreadCount > 0)
                                <span class="absolute top-1 right-1 h-2 w-2 bg-red-500 rounded-full"></span>
                            @endif
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span>Logs</span>
                        </a>

                        <a href="{{ route('reports.index') }}"
                            class="inline-flex items-center px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('reports.*') ? 'text-purple-600 border-b-2 border-purple-600' : 'text-gray-600 hover:text-gray-900 border-b-2 border-transparent' }}">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            <span>Reports</span>
                        </a>

                        <a href="{{ route('rcas.index') }}"
                            class="inline-flex items-center px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('rcas.*') ? 'text-orange-600 border-b-2 border-orange-600' : 'text-gray-600 hover:text-gray-900 border-b-2 border-transparent' }}">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                            </svg>
                            <span>RCA</span>
                        </a>

                        <!-- Extra Dropdown -->
                        <div class="relative" x-data="{ extraOpen: false }">
                            <button @click="extraOpen = !extraOpen"
                                class="inline-flex items-center px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('smart-parser.*') || request()->routeIs('contacts.*') || request()->routeIs('temporary-sites.*') || request()->routeIs('sites.*') || request()->routeIs('fbb-islands.*') ? 'text-green-600 border-b-2 border-green-600' : 'text-gray-600 hover:text-gray-900 border-b-2 border-transparent' }}">
                                <span>More</span>
                                <svg class="w-4 h-4 ml-1 transition-transform" :class="{ 'rotate-180': extraOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>

                            <!-- Dropdown Menu -->
                            <div x-show="extraOpen" @click.away="extraOpen = false"
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="opacity-0 scale-95"
                                x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="opacity-100 scale-100"
                                x-transition:leave-end="opacity-0 scale-95"
                                class="absolute left-0 z-50 mt-1 w-56 rounded-lg shadow-lg bg-white border border-gray-200"
                                style="display: none;">
                                <div class="py-1">
                                    @if(auth()->user()->canEditIncidents())
                                    <a href="{{ route('smart-parser.index') }}"
                                        class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 {{ request()->routeIs('smart-parser.*') ? 'bg-gray-50 text-gray-900' : '' }}">
                                        <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                                        </svg>
                                        <span>AI Logger</span>
                                    </a>
                                    @endif

                                    <a href="{{ route('contacts.index') }}"
                                        class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 {{ request()->routeIs('contacts.*') ? 'bg-gray-50 text-gray-900' : '' }}">
                                        <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                        <span>Phone Book</span>
                                    </a>

                                    <a href="{{ route('temporary-sites.index') }}"
                                        class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 {{ request()->routeIs('temporary-sites.*') ? 'bg-gray-50 text-gray-900' : '' }}">
                                        <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                        </svg>
                                        <span>Temporary Sites</span>
                                    </a>

                                    <a href="{{ route('sites.index') }}"
                                        class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 {{ request()->routeIs('sites.*') ? 'bg-gray-50 text-gray-900' : '' }}">
                                        <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                        <span>Sites</span>
                                    </a>

                                    <a href="{{ route('fbb-islands.index') }}"
                                        class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 {{ request()->routeIs('fbb-islands.*') ? 'bg-gray-50 text-gray-900' : '' }}">
                                        <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span>FBB Islands</span>
                                    </a>
                                </div>
                            </div>
                        </div>

                        @if(Auth::user()->canManageUsers())
                            <a href="{{ route('users.index') }}"
                                class="inline-flex items-center px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('users.*') ? 'text-indigo-600 border-b-2 border-indigo-600' : 'text-gray-600 hover:text-gray-900 border-b-2 border-transparent' }}">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                <span>Users</span>
                            </a>
                        @endif
                    </div>
                </div>

                <!-- User Menu -->
                <div class="flex items-center space-x-3 flex-shrink-0">
                    <!-- User Dropdown -->
                    <div class="relative" x-data="{ userOpen: false }">
                        <!-- User Avatar/Name Button -->
                        <button @click="userOpen = !userOpen"
                            class="flex items-center space-x-2 px-2 py-1.5 rounded-lg hover:bg-gray-50 focus:outline-none transition-colors">
                            <div class="hidden lg:flex flex-col items-end">
                                <span class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</span>
                                <span class="text-xs text-gray-500">{{ Auth::user()->getRoleDisplayName() }}</span>
                            </div>
                            <div class="w-8 h-8 bg-red-600 rounded-full flex items-center justify-center">
                                <span class="text-white text-sm font-medium">{{ substr(Auth::user()->name, 0, 1) }}</span>
                            </div>
                            <svg class="w-4 h-4 text-gray-400 transition-transform" :class="{ 'rotate-180': userOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="userOpen"
                            @click.away="userOpen = false"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="opacity-100 scale-100"
                            x-transition:leave-end="opacity-0 scale-95"
                            class="absolute right-0 z-50 mt-2 w-56 rounded-lg shadow-lg bg-white border border-gray-200"
                            style="display: none;">
                            <div class="py-1">
                                <div class="px-4 py-3 border-b border-gray-100">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ Auth::user()->name }}</p>
                                    <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
                                </div>

                                <div class="py-1">
                                    <a href="{{ route('profile.edit') }}"
                                        class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                        <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        <span>Profile</span>
                                    </a>

                                    @if(Auth::user()->canEditIncidents())
                                        <a href="{{ route('incidents.create') }}"
                                            class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                            <span>New Incident</span>
                                        </a>
                                    @endif

                                    @if(Auth::user()->canManageUsers())
                                        <a href="{{ route('users.index') }}"
                                            class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                            </svg>
                                            <span>Manage Users</span>
                                        </a>
                                    @endif
                                </div>

                                <div class="border-t border-gray-100">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit"
                                            class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                            </svg>
                                            <span>Sign Out</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Mobile menu button -->
                    <div class="lg:hidden">
                        <button @click="open = ! open"
                            class="p-2 rounded-lg text-gray-600 hover:bg-gray-50 focus:outline-none">
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
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
            class="hidden lg:hidden bg-white/95 backdrop-blur-md border-t border-gray-200/50">
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
                        <div class="text-xs font-heading font-medium 
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
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-1.964-1.333-2.732 0L3.732 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <span>Incidents</span>
                </a>

                <a href="{{ route('logs.index') }}"
                    class="flex items-center px-4 py-3 rounded-xl text-base font-medium transition-all duration-300 transform hover:scale-105 {{ request()->routeIs('logs.*') ? 'bg-gradient-to-r from-blue-50 to-blue-100 text-blue-700 shadow-md border border-blue-200/50' : 'text-gray-700 hover:text-blue-600 hover:bg-gradient-to-r hover:from-blue-50 hover:to-blue-100' }}">
                    <div class="relative">
                        @php
                            $unreadCount = \App\Models\Incident::whereNotNull('timeline')
                                ->get()
                                ->filter(function($incident) {
                                    return $incident->hasUnreadTimelineUpdates();
                                })
                                ->count();
                        @endphp
                        @if($unreadCount > 0)
                            <div class="absolute -top-1 -right-1 h-2 w-2 bg-red-500 rounded-full border border-white animate-pulse shadow-lg" title="{{ $unreadCount }} unread updates"></div>
                        @endif
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <span>Logs</span>
                </a>

                <a href="{{ route('reports.index') }}"
                    class="flex items-center px-4 py-3 rounded-xl text-base font-medium transition-all duration-300 transform hover:scale-105 {{ request()->routeIs('reports.*') ? 'bg-gradient-to-r from-purple-50 to-purple-100 text-purple-700 shadow-md border border-purple-200/50' : 'text-gray-700 hover:text-purple-600 hover:bg-gradient-to-r hover:from-purple-50 hover:to-purple-100' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <span>Reports</span>
                </a>

                <a href="{{ route('rcas.index') }}"
                    class="flex items-center px-4 py-3 rounded-xl text-base font-medium transition-all duration-300 transform hover:scale-105 {{ request()->routeIs('rcas.*') ? 'bg-gradient-to-r from-orange-50 to-orange-100 text-orange-700 shadow-md border border-orange-200/50' : 'text-gray-700 hover:text-orange-600 hover:bg-gradient-to-r hover:from-orange-50 hover:to-orange-100' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                    <span>RCA</span>
                </a>

                @if(Auth::user()->canEditIncidents())
                <a href="{{ route('smart-parser.index') }}"
                    class="flex items-center px-4 py-3 rounded-xl text-base font-medium transition-all duration-300 transform hover:scale-105 {{ request()->routeIs('smart-parser.*') ? 'bg-gradient-to-r from-purple-50 to-purple-100 text-purple-700 shadow-md border border-purple-200/50' : 'text-gray-700 hover:text-purple-600 hover:bg-gradient-to-r hover:from-purple-50 hover:to-purple-100' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                    </svg>
                    <span>AI Logger</span>
                </a>
                @endif

                <a href="{{ route('contacts.index') }}"
                    class="flex items-center px-4 py-3 rounded-xl text-base font-medium transition-all duration-300 transform hover:scale-105 {{ request()->routeIs('contacts.*') ? 'bg-gradient-to-r from-blue-50 to-blue-100 text-blue-700 shadow-md border border-blue-200/50' : 'text-gray-700 hover:text-blue-600 hover:bg-gradient-to-r hover:from-blue-50 hover:to-blue-100' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span>Phone Book</span>
                </a>

                <a href="{{ route('temporary-sites.index') }}"
                    class="flex items-center px-4 py-3 rounded-xl text-base font-medium transition-all duration-300 transform hover:scale-105 {{ request()->routeIs('temporary-sites.*') ? 'bg-gradient-to-r from-amber-50 to-amber-100 text-amber-700 shadow-md border border-amber-200/50' : 'text-gray-700 hover:text-amber-600 hover:bg-gradient-to-r hover:from-amber-50 hover:to-amber-100' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                    <span>Temporary Sites</span>
                </a>

                <a href="{{ route('sites.index') }}"
                    class="flex items-center px-4 py-3 rounded-xl text-base font-medium transition-all duration-300 transform hover:scale-105 {{ request()->routeIs('sites.*') ? 'bg-gradient-to-r from-green-50 to-green-100 text-green-700 shadow-md border border-green-200/50' : 'text-gray-700 hover:text-green-600 hover:bg-gradient-to-r hover:from-green-50 hover:to-green-100' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <span>Sites</span>
                </a>

                <a href="{{ route('fbb-islands.index') }}"
                    class="flex items-center px-4 py-3 rounded-xl text-base font-medium transition-all duration-300 transform hover:scale-105 {{ request()->routeIs('fbb-islands.*') ? 'bg-gradient-to-r from-purple-50 to-purple-100 text-purple-700 shadow-md border border-purple-200/50' : 'text-gray-700 hover:text-purple-600 hover:bg-gradient-to-r hover:from-purple-50 hover:to-purple-100' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>FBB Islands</span>
                </a>

                @if(Auth::user()->canManageUsers())
                    <a href="{{ route('users.index') }}"
                        class="flex items-center px-4 py-3 rounded-xl text-base font-medium transition-all duration-300 transform hover:scale-105 {{ request()->routeIs('users.*') ? 'bg-gradient-to-r from-indigo-50 to-indigo-100 text-indigo-700 shadow-md border border-indigo-200/50' : 'text-gray-700 hover:text-indigo-600 hover:bg-gradient-to-r hover:from-indigo-50 hover:to-indigo-100' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <span>Users</span>
                    </a>
                @endif

                <a href="{{ route('profile.edit') }}"
                    class="flex items-center px-4 py-3 rounded-xl text-base font-heading font-medium text-gray-700 hover:text-red-600 hover:bg-gradient-to-r hover:from-red-50 hover:to-red-100 transition-all duration-300 transform hover:scale-105">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span>Profile Settings</span>
                </a>

                <form method="POST" action="{{ route('logout') }}" class="mt-4 pt-4 border-t border-gray-200">
                    @csrf
                    <button type="submit"
                        class="flex items-center w-full px-4 py-3 rounded-xl text-base font-heading font-medium text-gray-700 hover:text-red-600 hover:bg-gradient-to-r hover:from-red-50 hover:to-red-100 transition-all duration-300 transform hover:scale-105">
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

    <!-- Mobile Bottom Navigation Bar -->
    <nav x-data="{ showMoreMenu: false }"
         class="fixed bottom-0 left-0 right-0 z-40 md:hidden bg-white border-t-2 border-gray-200 shadow-2xl">
        <div class="grid grid-cols-5 h-16">
            <!-- Incidents -->
            <a href="{{ route('incidents.index') }}"
               class="flex flex-col items-center justify-center space-y-1 {{ request()->routeIs('incidents.*') ? 'text-red-600 bg-red-50/50' : 'text-gray-600' }} transition-all duration-200 hover:bg-gray-50">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-1.964-1.333-2.732 0L3.732 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <span class="text-xs font-heading font-medium">Incidents</span>
            </a>

            <!-- Logs -->
            <a href="{{ route('logs.index') }}"
               class="flex flex-col items-center justify-center space-y-1 {{ request()->routeIs('logs.*') ? 'text-blue-600 bg-blue-50/50' : 'text-gray-600' }} transition-all duration-200 hover:bg-gray-50">
                <div class="relative">
                    @php
                        $unreadCount = \App\Models\Incident::whereNotNull('timeline')
                            ->get()
                            ->filter(function($incident) {
                                return $incident->hasUnreadTimelineUpdates();
                            })
                            ->count();
                    @endphp
                    @if($unreadCount > 0)
                        <div class="absolute -top-1 -right-1 h-2 w-2 bg-red-500 rounded-full border border-white animate-pulse shadow-lg" title="{{ $unreadCount }} unread updates"></div>
                    @endif
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <span class="text-xs font-heading font-medium">Logs</span>
            </a>

            <!-- Home -->
            <a href="{{ route('home') }}"
               class="flex flex-col items-center justify-center space-y-1 {{ request()->routeIs('home') ? 'text-red-600 bg-red-50/50' : 'text-gray-600' }} transition-all duration-200 hover:bg-gray-50">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span class="text-xs font-heading font-medium">Home</span>
            </a>

            <!-- RCA -->
            <a href="{{ route('rcas.index') }}"
               class="flex flex-col items-center justify-center space-y-1 {{ request()->routeIs('rcas.*') ? 'text-orange-600 bg-orange-50/50' : 'text-gray-600' }} transition-all duration-200 hover:bg-gray-50">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
                <span class="text-xs font-heading font-medium">RCA</span>
            </a>

            <!-- Phone Book -->
            <a href="{{ route('contacts.index') }}"
               class="flex flex-col items-center justify-center space-y-1 {{ request()->routeIs('contacts.*') ? 'text-blue-600 bg-blue-50/50' : 'text-gray-600' }} transition-all duration-200 hover:bg-gray-50">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <span class="text-xs font-heading font-medium">Phone Book</span>
            </a>
        </div>

        <!-- More Menu Overlay -->
        <div x-show="showMoreMenu"
             x-cloak
             @click.away="showMoreMenu = false"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 md:hidden bg-black/50 backdrop-blur-sm">
            <!-- Menu Panel -->
            <div @click.stop
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="transform translate-y-full"
                 x-transition:enter-end="transform translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="transform translate-y-0"
                 x-transition:leave-end="transform translate-y-full"
                 class="absolute bottom-0 left-0 right-0 bg-white rounded-t-3xl shadow-2xl max-h-[80vh] overflow-y-auto">

                <!-- Handle Bar -->
                <div class="flex justify-center pt-3 pb-2">
                    <div class="w-12 h-1.5 bg-gray-300 rounded-full"></div>
                </div>

                <!-- Header -->
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="font-heading text-lg font-heading font-bold text-gray-900">Menu</h3>
                        <button @click="showMoreMenu = false"
                                class="p-2 hover:bg-gray-100 rounded-full transition-colors">
                            <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- User Info -->
                <div class="px-6 py-4 bg-gradient-to-r from-gray-50/80 to-red-50/60 border-b border-gray-200">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-red-600 rounded-full flex items-center justify-center shadow-lg">
                            <span class="text-white font-medium text-lg">{{ substr(Auth::user()->name, 0, 1) }}</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="font-semibold text-gray-900 truncate">{{ Auth::user()->name }}</p>
                            <p class="text-sm text-gray-600 truncate">{{ Auth::user()->email }}</p>
                            <p class="text-xs font-heading font-medium
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
                <div class="px-4 py-4 space-y-2">
                    <a href="{{ route('profile.edit') }}" @click="showMoreMenu = false"
                       class="flex items-center px-4 py-3 rounded-xl text-base font-heading font-medium text-gray-700 hover:text-red-600 hover:bg-gradient-to-r hover:from-red-50 hover:to-red-100 transition-all duration-200">
                        <svg class="w-6 h-6 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                            </path>
                        </svg>
                        <span>Profile Settings</span>
                    </a>

                    @if(Auth::user()->canEditIncidents())
                        <a href="{{ route('incidents.create') }}" @click="showMoreMenu = false"
                           class="flex items-center px-4 py-3 rounded-xl text-base font-heading font-medium text-gray-700 hover:text-red-600 hover:bg-gradient-to-r hover:from-red-50 hover:to-red-100 transition-all duration-200">
                            <svg class="w-6 h-6 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            <span>New Incident</span>
                        </a>
                    @endif

                    @if(Auth::user()->canManageUsers())
                        <a href="{{ route('users.index') }}" @click="showMoreMenu = false"
                           class="flex items-center px-4 py-3 rounded-xl text-base font-heading font-medium text-gray-700 hover:text-indigo-600 hover:bg-gradient-to-r hover:from-indigo-50 hover:to-indigo-100 transition-all duration-200">
                            <svg class="w-6 h-6 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <span>User Management</span>
                        </a>
                    @endif

                    <div class="my-2 border-t border-gray-200"></div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="flex items-center w-full px-4 py-3 rounded-xl text-base font-heading font-medium text-gray-700 hover:text-red-600 hover:bg-gradient-to-r hover:from-red-50 hover:to-red-100 transition-all duration-200">
                            <svg class="w-6 h-6 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                </path>
                            </svg>
                            <span>Sign Out</span>
                        </button>
                    </form>
                </div>

                <!-- Safe area padding for iOS notch -->
                <div class="h-8"></div>
            </div>
        </div>
    </nav>
@endauth