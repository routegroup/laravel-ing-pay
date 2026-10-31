<form method="post" action="{{ $action }}">
@foreach ($fields as $name => $value)
    <input type="hidden" name="{{ $name }}" value="{{ $value }}">
@endforeach
    <button type="submit">Pay</button>
</form>
