@if(auth()->user()->role === 'admin')
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.articles.*') ? 'active' : '' }}" 
           href="{{ route('admin.articles.index') }}">
            <i class="fas fa-newspaper me-2"></i>Article Management
        </a>
    </li>
@endif 