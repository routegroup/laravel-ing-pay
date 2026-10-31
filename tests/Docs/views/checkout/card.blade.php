<div id="imoje-widget__wrapper"></div>
<script src="{{ $script }}" id="{{ $scriptId }}"
@foreach ($fields as $name => $value)
    data-{{ \Illuminate\Support\Str::kebab($name) }}="{{ $value }}"
@endforeach
></script>
