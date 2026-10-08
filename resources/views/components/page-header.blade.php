@props(['title'])

<div class="page-title">
    <div class="title_left">
        <h3>{{ $title }}</h3>
    </div>
    <div class="title_right text-right">
        {{ $slot }}
    </div>
</div>
<div class="clearfix"></div>
<br>
