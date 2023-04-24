<form
    method="POST"
    action="{{ route('logout') }}"
>
    @csrf
    @method('DELETE')
    <button
        class="btn-warning"
        type="submit"
    >logout</button>
</form>
