@forelse($escapes as $escape)
<tr>
    <td>{{ $escape->id }}</td>
    <td>{{ $escape->patient_code ?? '-' }}</td>
    <td>{{ $escape->full_name ?? '-' }}</td>
    <td>{{ $escape->contact_number ?? '-' }}</td>
    <td>{{ \Carbon\Carbon::parse($escape->escape_datetime)->format('d-m-Y H:i') }}</td>
    <td>
        @if($escape->video_footage)
            <a href="{{ asset($escape->video_footage) }}" target="_blank">View Video</a>
        @else N/A @endif
    </td>
    <td>
        @if($escape->police_fir_copy)
            <a href="{{ asset($escape->police_fir_copy) }}" target="_blank">View FIR</a>
        @else N/A @endif
    </td>
    <td>
        @if($escape->file_attach)
            <a href="{{ asset($escape->file_attach) }}" target="_blank">View File</a>
        @else N/A @endif
    </td>
    <td class="d-flex">
        <a href="{{ route('escape.edit', $escape->id) }}" class="btn btn-sm btn-warning"><i class="fa-solid fa-pen-to-square"></i></a>
        <form id="delete-form-{{ $escape->id }}" action="{{ route('escape.destroy', $escape->id) }}" method="POST" style="display:inline;">
            @csrf
            @method('DELETE')
            <button type="button" class="btn btn-sm btn-danger deleteEscapeBtn" data-id="{{ $escape->id }}">
                <i class="fa-solid fa-trash"></i>
            </button>
        </form>
    </td>
</tr>
@empty

@endforelse
