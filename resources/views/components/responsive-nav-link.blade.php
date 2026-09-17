@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full border-l-4 border-indigo-600 bg-indigo-50 py-2 pe-4 ps-3 text-start text-base font-semibold text-indigo-700 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-indigo-500'
            : 'block w-full border-l-4 border-transparent py-2 pe-4 ps-3 text-start text-base font-medium text-slate-600 transition hover:border-indigo-200 hover:bg-slate-50 hover:text-indigo-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-indigo-500';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
