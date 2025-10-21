<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $family->name }} Family Book</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 2rem; }
        h1, h2 { color: #1f2937; }
        .person { margin-bottom: 1.5rem; }
        .meta { color: #4b5563; font-size: 0.9rem; }
    </style>
</head>
<body>
    <h1>{{ $family->name }} Family Book</h1>
    <p>Generated on {{ now()->toDayDateTimeString() }}</p>

    @foreach($people as $person)
        <div class="person">
            <h2>{{ $person->display_name }}</h2>
            <p class="meta">
                @if($person->birth_date)
                    Born: {{ \\Illuminate\\Support\\Carbon::parse($person->birth_date)->toFormattedDateString() }}
                @endif
                @if($person->death_date)
                    &mdash; Died: {{ \\Illuminate\\Support\\Carbon::parse($person->death_date)->toFormattedDateString() }}
                @endif
            </p>
            @if($person->meta)
                <pre>{{ json_encode($person->meta, JSON_PRETTY_PRINT) }}</pre>
            @endif
        </div>
    @endforeach
</body>
</html>
