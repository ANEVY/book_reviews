@if ($rating)
    @for ($i=1; $i <= 5; $i++)
    {{($i < round($rating)) ? '#' : '*'}}
    @endfor
@else
    <span>No rating found</span>
@endif