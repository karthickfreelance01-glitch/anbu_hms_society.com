@extends('layout.admin')
@section('content')
<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')

    <div class="page-wrapper">
        <div class="content pb-0">
            <div class="container-fluid">
                <div class="card shadow-lg border-0 rounded-4 p-4 mt-4 animate__animated animate__fadeIn">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold text-gradient">🏥 Medical Equipment Form</h2>
                        <p class="text-muted">Add or update medical equipment details</p>
                        <hr class="mt-3 mb-4 mx-auto w-50">
                    </div>

                    <form id="equipmentForm">
                        @csrf
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">Equipment Name <span class="text-danger">*</span></label>
                                <input type="text" name="equipment_name" class="form-control" placeholder="Enter equipment name" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Stock <span class="text-danger">*</span></label>
                                <input type="number" name="stock" class="form-control" placeholder="0" min="0" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Repair</label>
                                <input type="text" name="repair" class="form-control" placeholder="Any repair details">
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label">Purchase Date <span class="text-danger">*</span></label>
                                <input type="date" name="purchase_date" class="form-control" required>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">Remarks</label>
                                <textarea name="remarks" class="form-control" rows="2" placeholder="Additional notes..."></textarea>
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-primary btn-lg px-5 shadow-lg">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function(){
    $('#equipmentForm').submit(function(e){
        e.preventDefault();
        const formData = new FormData(this);
        const btn = $(this).find('button[type="submit"]');
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Saving...');

        $.ajax({
            url: "{{ url('equipments/store') }}",
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function(){
                Swal.fire({ icon: 'success', title: 'Saved!', text: 'Equipment added successfully!' })
                    .then(() => window.location.href = "{{ route('equipments.index') }}");
            },
            error: function(xhr){
                btn.prop('disabled', false).html('Submit');
                if(xhr.status === 422){
                    let msg = '';
                    $.each(xhr.responseJSON.errors, (k,v)=> msg += v[0]+'<br>');
                    Swal.fire({ icon:'error', title:'Validation Error', html:msg });
                } else {
                    Swal.fire({ icon:'error', title:'Error', text:'Something went wrong!' });
                }
            }
        });
    });
});
</script>

<style>
.text-gradient { background: linear-gradient(45deg,#4e73df,#1cc88a); -webkit-background-clip: text; color: transparent; }
.btn-gradient { background: linear-gradient(135deg,#4e73df,#1cc88a); color:white; border:none; transition:0.3s; }
.btn-gradient:hover { transform:scale(1.03); box-shadow:0 0 12px rgba(76,175,80,0.3); }
.section-title { font-weight:700; color:#4e73df; border-left:4px solid #1cc88a; padding-left:10px; }
</style>
@endsection
