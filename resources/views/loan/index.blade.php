@extends('layout.admin')

@section('content')
<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')

    <div class="page-wrapper">
        <div class="content pb-0">
            <div class="container-fluid">
                <div class="card shadow-lg rounded-4 p-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4>Loan List</h4>
                        <button class="btn btn-gradient" data-bs-toggle="modal" data-bs-target="#loanModal">
                            + Add New Loan
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped align-middle" id="loanTable">
                                <thead class="table-gradient text-white">
                                    <tr>
                                        <th>#</th>
                                        <th>Loan Amount (₹)</th>
                                        <th>Bank Name</th>
                                        <th>Loan Date</th>
                                        <th>Hospital Account</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($loans as $index => $loan)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>₹ {{ number_format($loan->loan_amount, 2) }}</td>
                                        <td>{{ $loan->bank_name }}</td>
                                        <td>{{ $loan->loan_date }}</td>
                                        <td>{{ $loan->payment_category }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-primary editLoan" data-id="{{ $loan->id }}">✎</button>
                                            <button class="btn btn-sm btn-danger removeLoan" data-id="{{ $loan->id }}">✖</button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Loan Modal -->
<div class="modal fade" id="loanModal" tabindex="-1" aria-labelledby="loanModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 shadow-lg">
      <div class="modal-header">
        <h5 class="modal-title fw-bold" id="loanModalLabel">Add/Edit Loan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="loanForm">
          @csrf
          <input type="hidden" name="loan_id" id="loan_id">
          <div class="modal-body">
              <div class="row g-3">
                  <div class="col-md-12">
                      <label>Loan Amount (₹)</label>
                      <input type="number" name="loan_amount" id="loan_amount" class="form-control" step="0.01" min="0" required>
                  </div>
                  <div class="col-md-12">
                      <label>Bank Name</label>
                      <input type="text" name="bank_name" id="bank_name" class="form-control" required>
                  </div>
                  <div class="col-md-12">
                      <label>Loan Date</label>
                      <input type="date" name="loan_date" id="loan_date" class="form-control" required>
                  </div>
                  <div class="col-md-12">
                      <label>Hospital Account</label>
                      <select name="hospital_account" id="hospital_account" class="form-select select2" required>
                          <option value="">-- Select Account --</option>
                          @foreach($payment_categories as $account)
                              <option value="{{ $account->id }}">{{ $account->payment_category }}</option>
                          @endforeach
                      </select>
                  </div>
              </div>
          </div>
          <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-gradient">Save</button>
          </div>
      </form>
    </div>
  </div>
</div>

<!-- JS Links -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
<link href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" rel="stylesheet"/>
<link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css" rel="stylesheet"/>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function(){
    $('.select2').select2({ width:'100%' });

    // Initialize DataTable with search left & export buttons right
    let table = $('#loanTable').DataTable({
        dom: "<'row mb-3'<'col-sm-6'f><'col-sm-6 text-end'B>>" +
             "<'row'<'col-sm-12'tr>>" +
             "<'row mt-3'<'col-sm-12'p>>",
        buttons: ['copy','csv','excel','pdf','print']
    });

    // Open Edit Modal
    $(document).on('click','.editLoan', function(){
        let id = $(this).data('id');
        $.get("{{ url('loan/edit') }}/"+id, function(res){
            $('#loan_id').val(res.id);
            $('#loan_amount').val(res.loan_amount);
            $('#bank_name').val(res.bank_name);
            $('#loan_date').val(res.loan_date);
            $('#hospital_account').val(res.hospital_payment_category).trigger('change');
            $('#loanModal').modal('show');
        }).fail(function(xhr){
            Swal.fire('Error', xhr.responseJSON?.message || 'Something went wrong', 'error');
        });
    });

    // Submit Add/Edit
    $('#loanForm').submit(function(e){
        e.preventDefault();
        let loan_id = $('#loan_id').val();
        let url = loan_id 
            ? "{{ url('loan/update') }}/" + loan_id
            : "{{ route('loan.store') }}";

        $.ajax({
            url: url,
            type: 'POST',
            data: $(this).serialize(),
            success: function(res){
                Swal.fire('Success', res.message, 'success').then(()=> location.reload());
            },
            error: function(xhr){
                let message = "Something went wrong!";
                if(xhr.responseJSON){
                    if(xhr.responseJSON.errors){
                        message = Object.values(xhr.responseJSON.errors).join("\n");
                    } else if(xhr.responseJSON.message){
                        message = xhr.responseJSON.message;
                    }
                }
                Swal.fire('Error', message, 'error');
            }
        });
    });

    // Delete Loan
    $(document).on('click','.removeLoan', function(){
        let id = $(this).data('id');
        Swal.fire({
            title:'Are you sure?',
            text:"This cannot be undone!",
            icon:'warning',
            showCancelButton:true,
            confirmButtonText:'Yes, delete!'
        }).then((result)=>{
            if(result.isConfirmed){
                $.ajax({
                    url:"{{ url('loan/destroy') }}/"+id,
                    type:'DELETE',
                    data:{ _token:"{{ csrf_token() }}" },
                    success:function(res){
                        Swal.fire('Deleted!', res.message, 'success').then(()=> location.reload());
                    },
                    error:function(xhr){
                        let message = xhr.responseJSON?.message || 'Something went wrong!';
                        Swal.fire('Error', message, 'error');
                    }
                });
            }
        });
    });

});
</script>

<style>
.btn-gradient {
    background: linear-gradient(135deg,#4e73df,#1cc88a);
    color:white;border:none;transition:0.3s;
}
.btn-gradient:hover{
    transform:scale(1.03);box-shadow:0 0 12px rgba(76,175,80,0.3);
}
.table-gradient{
    background: linear-gradient(45deg,#4e73df,#1cc88a);
}
</style>
@endsection
