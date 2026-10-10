@php
    $steps = \App\Enums\OrderStatus::cases();
    $currentIndex = array_search($status, $steps, true);
@endphp

<ol class="list-unstyled d-flex gap-2 mb-4 small" aria-label="Order progress">
    @foreach ($steps as $index => $step)
        <li class="flex-fill text-center py-2 rounded {{ $index <= $currentIndex ? 'bg-success text-white' : 'bg-light text-muted border' }}">
            {{ $step->label() }}
        </li>
    @endforeach
</ol>
