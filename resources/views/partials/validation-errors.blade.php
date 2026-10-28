@if ($errors->any())
    <div role="alert" aria-live="assertive" style="margin-bottom: 1rem; padding: 1rem; border-radius: 0.5rem; background-color: #fee2e2; color: #991b1b;">
        <strong>Não foi possível submeter a manifestação. Verifique estes campos:</strong>
        <ul style="margin: 0.5rem 0 0; padding-left: 1.25rem;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
