<tr data-id="{{ $expectation->id }}">
    <td>{{ $expectation->goal }}</td>
    <td>
        <input type="checkbox" class="toggle-status" data-id="{{ $expectation->id }}" {{ $expectation->status == 'completed' ? 'checked' : '' }}>
        <span>{{ ucfirst($expectation->status) }}</span>
    </td>
    <td>{{ $expectation->target_date->format('Y-m-d') }}</td>
    <td>{{ $expectation->completion_percentage }}%</td>
    <td>
        <button class="btn btn-sm btn-info edit-expectation" data-id="{{ $expectation->id }}"><i class="fa fa-edit"></i></button>
        <button class="btn btn-sm btn-danger delete-expectation" data-id="{{ $expectation->id }}"><i class="fa fa-trash"></i></button>
    </td>
</tr> 