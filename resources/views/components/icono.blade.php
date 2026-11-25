@props(['nombre', 'tam' => 20])
@php($svg = @file_get_contents(resource_path("iconos/{$nombre}.svg")) ?: '')
<span {{ $attributes->merge(['class' => 'icono']) }} style="width:{{ $tam }}px;height:{{ $tam }}px" aria-hidden="true">{!! preg_replace('/^<svg width="\d+" height="\d+"/', '<svg width="100%" height="100%"', trim($svg)) !!}</span>
