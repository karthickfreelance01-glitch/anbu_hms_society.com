@extends('layout.admin')

@section('content')
<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')

    <div class="page-wrapper">
        <div class="content py-4">

            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold">Appointment Report</h3>
                <a href="{{ url('appointment/create') }}" class="btn btn-primary btn-lg">
                    <i class="fa-solid fa-plus"></i> New Appointment
                </a>
            </div>

            <!-- Date Filter Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body d-flex align-items-center gap-3">
                    <label for="filterDate" class="form-label mb-0 fw-semibold">📅 Select Date:</label>
                    <input type="date" id="filterDate" class="form-control form-control-md" style="max-width: 250px;" />
                </div>
            </div>

            <!-- Appointment Table Card -->
            <div class="card shadow-sm border-0">
                <div class="card-body p-3">
                    <div class="table-responsive">
                        <table id="appointmentTable" class="table table-bordered align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Token No</th>
                                    <th>Patient ID</th>
                                    <th>Patient Name</th>
                                    <th>Disease Title</th>
                                    <th>Doctor Name</th>
                                    <th>Date & Time</th>
                                    <th>Message</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Data loads via AJAX --}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- JS Dependencies -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

<script>
$(document).ready(function() {
    var table = $('#appointmentTable').DataTable({
        dom: '<"d-flex justify-content-between align-items-center mb-3"<"search-box"f><"dt-buttons"B>>rtip',
        buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
        responsive: true,
        pagingType: 'simple_numbers',  // Bootstrap-like pagination style
        language: {
            paginate: {
                previous: '«',
                next: '»'
            },
            search: "",  // Remove default label
        }
    });

    // Move search box to left with label
    $('.search-box').html(`
        <label style="font-weight: bold;"> Search:</label>
        <input type="search" class="form-control form-control-sm ms-2" placeholder="Type to search..." aria-controls="appointmentTable" style="display: inline-block; width: auto;">
    `);

    // Bind input manually to DataTables search
    $('.search-box input').on('keyup', function () {
        table.search(this.value).draw();
    });

    function populateTable(data) {
        table.clear();
        $.each(data, function(index, appointment) {
            let statusHtml = '';
            if (appointment.status == '0') {
                statusHtml = '<span class="badge bg-danger">Cancelled</span>';
            } else if (appointment.status == '1') {
                statusHtml = '<span class="badge bg-warning">Pending</span>';
            } else if (appointment.status == '2') {
                statusHtml = '<span class="badge bg-success">Finished</span>';
                if (appointment.billing_status != 1) {
                    statusHtml += ` <a href="{{ url('appointment/billing') }}/${appointment.id}" class="btn btn-sm btn-info ms-2">Generate Bill</a>`;
                }
            }

            let actionButtons = `
                <a href="{{ url('appointment/edit') }}/${appointment.id}" class="btn btn-sm btn-white">
                    <i class="fa-solid fa-pen-to-square text-primary"></i>
                </a>
            `;

            if (appointment.status != '2') {
                actionButtons += `
                    <a href="javascript:void(0)" class="statusActionBtn btn btn-sm btn-white text-success"
                       data-url="{{ route('appointments.finish', '') }}/${appointment.id}"
                       data-message="mark this appointment as finished?">
                       <i class="fa-solid fa-check"></i>
                    </a>
                `;
            }

            if (appointment.status != '1') {
                actionButtons += `
                    <a href="javascript:void(0)" class="statusActionBtn btn btn-sm btn-white text-warning"
                       data-url="{{ route('appointments.pending', '') }}/${appointment.id}"
                       data-message="set this appointment to pending?">
                       <i class="fa-solid fa-clock"></i>
                    </a>
                `;
            }

            if (appointment.status != '0') {
                actionButtons += `
                    <a href="javascript:void(0)" class="statusActionBtn btn btn-sm btn-white text-danger"
                       data-url="{{ route('appointments.cancel', '') }}/${appointment.id}"
                       data-message="cancel this appointment?">
                       <i class="fa-solid fa-xmark"></i>
                    </a>
                `;
            }

            table.row.add([
                index + 1,
                appointment.token_no,
                appointment.patient_id,
                appointment.full_name,
                appointment.symptoms_title || '-',
                appointment.staff_name || '-',
                appointment.appointment_datetime,
                appointment.message || '-',
                statusHtml,
                `<div class="d-flex gap-1 justify-content-center">${actionButtons}</div>`
            ]);
        });
        table.draw();
    }

    $('#filterDate').on('change', function() {
        var selectedDate = $(this).val();
        if (selectedDate) {
            $.ajax({
                url: "{{ route('appointments.filterByDate') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    date: selectedDate
                },
                success: function(response) {
                    if (response && response.data) {
                        populateTable(response.data);
                    }
                },
                error: function() {
                    Swal.fire('Error!', 'Unable to fetch data', 'error');
                }
            });
        }
    });

    $(document).on('click', '.statusActionBtn', function(e) {
        e.preventDefault();
        let url = $(this).data('url');
        let message = $(this).data('message');

        Swal.fire({
            title: 'Confirm Action',
            text: "Are you sure you want to " + message,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, proceed!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: { _token: "{{ csrf_token() }}" },
                    success: function(response) {
                        Swal.fire('Success!', response.message, 'success').then(() => location.reload());
                    },
                    error: function() {
                        Swal.fire('Error!', 'Something went wrong', 'error');
                    }
                });
            }
        });
    });
});
</script>
@endsection
