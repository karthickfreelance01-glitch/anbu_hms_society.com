@extends('layout.admin')
@section('content')
<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')

    <div class="page-wrapper">
        <div class="content">
            <div class="d-flex justify-content-between mb-3">
                <h4>Patient Payment Report</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Payments</li>
                </ol>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <h4 class="mb-3">Payments Summary</h4>

                    <!-- Filters -->
                    <form id="filterForm" class="row g-2 mb-3">
                        <div class="col-md-3">
                            <label>From Date</label>
                            <input type="date" id="from_date" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label>To Date</label>
                            <input type="date" id="to_date" class="form-control">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="button" id="resetFilter" class="btn btn-secondary me-2">Reset Filters</button>
                            <button type="button" id="printTable" class="btn btn-primary">Print</button>
                        </div>
                    </form>

                    <!-- Table -->
                    <div class="table-responsive" id="printableArea">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Patient ID</th>
                                    <th>Patient Name</th>
                                    <th>Contact No</th>
                                    <th>Total Amount</th>
                                    <th>Paid Amount</th>
                                    <th>Pending Amount</th>
                                    <th>Next Due Date</th>
                                </tr>
                            </thead>
                            <tbody id="billingTableBody">
                                <tr>
                                    <td colspan="7" class="text-center">Select date range to view records</td>
                                </tr>
                            </tbody>
                            <tfoot id="billingTableFooter">
                                <!-- Overall totals will go here -->
                            </tfoot>
                        </table>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(function(){
    function fetchBillings(){
        var from_date = $('#from_date').val();
        var to_date   = $('#to_date').val();

        if(!from_date && !to_date) {
            $('#billingTableBody').html('<tr><td colspan="7" class="text-center">Select date range to view records</td></tr>');
            $('#billingTableFooter').html('');
            return;
        }

        $.ajax({
            url: "{{ route('billing.reports') }}",
            type: "GET",
            data: { from_date: from_date, to_date: to_date },
            dataType: "json",
            success: function(response){
                if(response.html !== undefined){
                    $('#billingTableBody').html(response.html.body || '<tr><td colspan="7" class="text-center">No records found</td></tr>');
                    $('#billingTableFooter').html(response.html.footer || '');
                } else {
                    $('#billingTableBody').html('<tr><td colspan="7" class="text-center">No records found</td></tr>');
                    $('#billingTableFooter').html('');
                }
            },
            error: function(xhr){
                console.error(xhr.responseText);
                $('#billingTableBody').html('<tr><td colspan="7" class="text-center">Error fetching records</td></tr>');
                $('#billingTableFooter').html('');
            }
        });
    }

    $('#from_date, #to_date').on('change', fetchBillings);

    $('#resetFilter').on('click', function(){
        $('#filterForm')[0].reset();
        $('#billingTableBody').html('<tr><td colspan="7" class="text-center">Select date range to view records</td></tr>');
        $('#billingTableFooter').html('');
    });

    $('#printTable').on('click', function(){
        var printContents = document.getElementById('printableArea').innerHTML;
        var originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
        location.reload(); // reload to restore JS
    });
});
</script>
@endsection
