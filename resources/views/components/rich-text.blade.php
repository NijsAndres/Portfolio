@props(['text' => '', 'class' => ''])
{{-- Splits a plain-text field into paragraphs on blank lines and converts
     `**bold**` markers into <strong>. The paragraph is escaped first, so only
     our own <strong> tags are ever emitted — no raw HTML from the database. --}}
@foreach (preg_split('/\n\s*\n/', trim((string) $text)) as $paragraph)
<p class="{{ $class }}">{!! preg_replace('/\*\*(.+?)\*\*/s', '<strong>$1</strong>', e(trim($paragraph))) !!}</p>
@endforeach
