@forelse($discharges as $index => $discharge)
<tr>
    <td>{{ $index + 1 }}</td>
    <td>{{ $discharge->patient_code }}</td>
    <td>{{ $discharge->full_name }}</td>
    <td>{{ $discharge->admission_datetime }}</td>
    <td>{{ $discharge->dishcharge_datetime }}</td>
    <td>{{ $discharge->duration }} Months</td>
    <td>{{ $discharge->payment_mode }}</td>
    <td>
        <a href="{{ url('discharges/summary/'.$discharge->id) }}" target="_blank" class="btn btn-info btn-sm mb-2">📝 Print Summary</a>
        <a href="{{ url('discharges/bill/'.$discharge->id) }}" target="_blank" class="btn btn-primary btn-sm">💰 Print Bill</a>
    </td>
    <td>
        <a href="{{ url('discharges/edit/'.$discharge->id) }}" class="btn btn-sm btn-warning mb-1">
            <i class="fa-solid fa-pen-to-square"></i>
        </a>
        <button class="btn btn-sm btn-danger delete-discharge mb-1" data-id="{{ $discharge->id }}">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </td>
</tr>
@empty

@endforelse
