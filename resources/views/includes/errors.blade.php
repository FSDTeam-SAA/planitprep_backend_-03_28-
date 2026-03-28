<div>
    @if ($errors->any())
        <div class="alert alert-danger pb-0">
            <ul>
                @foreach ($errors->all() as $error)
                    <li class="text-start">
                        {{ $error }}
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
