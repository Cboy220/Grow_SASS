@foreach ($queries as $query)
    <tr data-id="{{ $query->feedback_query_id }}">
        <td>{{ $query->feedback_query_id }}</td>
        <td class="static-title">{{ $query->title }}</td>
        <td class="static-content">{{ $query->content }}</td>
        <td class="static-type" data-value="{{ $query->type }}">
            @php
                $type = '1';
                switch ($query->type) {
                    case '1':
                        $type='Numeric';
                        break;
                    case '2':
                        $type='Star';  
                        break;
                    case '3':
                        $type='Selector';
                        break;

                    default:
                        $type='Numeric';
                }
            @endphp
            {{ $type }}
        </td>
        <td class="static-range">{{ $query->range }}</td>
        <td class="static-weight">{{ number_format($query->weight, 1) }}</td>
        <td>
            <button class="btn btn-sm btn-primary edit-query"
                data-action-url="{{ route('settings.feedback.edit', $query->feedback_query_id) }}"
                data-id="{{ $query->feedback_query_id }}">
                <i class="fas fa-edit"></i>
            </button>
            <button class="btn btn-sm btn-danger delete-query"
                data-action-url="{{ route('settings.feedback.delete', $query->feedback_query_id) }}"
                data-id="{{ $query->feedback_query_id }}">
                <i class="fas fa-trash-alt"></i>
            </button>
        </td>
    </tr>
@endforeach
@if ($queries->isEmpty())
    <tr>
        <td colspan="7" class="text-center text-muted">No queries found.</td>
    </tr>
@endif
