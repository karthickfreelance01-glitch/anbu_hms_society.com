@extends('layout.admin')
@section('content')
<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')

    <div class="page-wrapper">
        <div class="content container-fluid">
            
            <!-- Header & Breadcrumb -->
            <div class="d-flex align-items-sm-center flex-sm-row flex-column gap-2 mb-4">
                <div class="flex-grow-1">
                    <h4 class="fw-bold text-gradient mb-0">🛠 Edit Medical Equipment</h4>
                </div>
                <div class="text-end">
                    <ol class="breadcrumb m-0 py-0">
                        <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ url('equipments') }}">Equipments</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                </div>
            </div>

            <!-- Edit Form Card -->
            <div class="card shadow border-0 rounded-4">
                <div class="card-body">
                    <form method="POST" action="{{ route('equipments.update', $equipment->id) }}" class="needs-validation" novalidate>
                        @csrf
                       

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="equipment_name" class="form-label fw-semibold">Equipment Name <span class="text-danger">*</span></label>
                                <input type="text" name="equipment_name" id="equipment_name" class="form-control" value="{{ old('equipment_name', $equipment->equipment_name) }}" required>
                            </div>

                            <div class="col-md-3">
                                <label for="stock" class="form-label fw-semibold">Stock <span class="text-danger">*</span></label>
                                <input type="number" name="stock" id="stock" class="form-control" value="{{ old('stock', $equipment->stock) }}" required>
                            </div>

                            <div class="col-md-3">
                                <label for="repair" class="form-label fw-semibold">Repair</label>
                                <input type="number" name="repair" id="repair" class="form-control" value="{{ old('repair', $equipment->repair) }}">
                            </div>

                            <div class="col-md-4">
                                <label for="purchase_date" class="form-label fw-semibold">Purchase Date</label>
                                <input type="date" name="purchase_date" id="purchase_date" class="form-control" value="{{ old('purchase_date', $equipment->purchase_date) }}">
                            </div>

                            <div class="col-md-8">
                                <label for="remarks" class="form-label fw-semibold">Remarks</label>
                                <textarea name="remarks" id="remarks" class="form-control" rows="2">{{ old('remarks', $equipment->remarks) }}</textarea>
                            </div>
                        </div>

                        <div class="mt-4 d-flex justify-content-end gap-2">
                            <a href="{{ url('equipments') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-gradient">Update</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- JS + SweetAlert -->
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    // Bootstrap validation
    (function () {
        'use strict'
        const forms = document.querySelectorAll('.needs-validation')
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                } else {
                    event.preventDefault();
                    Swal.fire({
                        icon: 'success',
                        title: 'Equipment updated successfully!',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    setTimeout(() => form.submit(), 1200);
                }
                form.classList.add('was-validated')
            }, false)
        })
    })()
});
</script>

<style>
.text-gradient {
    background: linear-gradient(45deg, #4e73df, #1cc88a);
    -webkit-background-clip: text;
    color: transparent;
}
.btn-gradient {
    background: linear-gradient(135deg, #4e73df, #1cc88a);
    color: white;
    border: none;
    transition: 0.3s;
}
.btn-gradient:hover {
    transform: scale(1.03);
    box-shadow: 0 0 12px rgba(76, 175, 80, 0.3);
}
</style>
@endsection
