<form method="POST" action="{{ route('admin.login.submit') }}">
    @csrf

    <input
        type="password"
        name="password"
        placeholder="Admin Password"
        class="border p-2 rounded"
    >

    <button type="submit">
        Login
    </button>
</form>


<a href="{{ route('admin.logout') }}">
    Logout
</a>
