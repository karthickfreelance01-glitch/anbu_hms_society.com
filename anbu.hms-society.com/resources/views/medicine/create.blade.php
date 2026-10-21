@extends('layout.admin')
@section('content')
<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')
    <div class="page-wrapper">
        <div class="content pb-0">
            <div class="container-fluid">

                <h3 class="text-primary fw-bold mb-4">Add Multiple Medicines</h3>

                <div class="card shadow-lg rounded-4 p-4">
                    <form action="{{ route('medicine.store') }}" method="POST">
                        @csrf

                        <!-- Vendor Selection -->
                        <div class="mb-3">
                            <label for="vendor_id" class="form-label fw-bold">Select Vendor</label>
                            <select name="vendor_id" id="vendor_id" class="form-select select2" required>
                                <option value="">-- Select Vendor --</option>
                                @foreach($vendors as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered align-middle" id="medicineTable">
                                <thead class="table-primary text-center">
                                    <tr>
                                        <th style="width:20%;">Drug Name</th>
                                        <th>Category</th>
                                        <th>Mfg Date</th>
                                        <th>Expiry Date</th>
                                        <th>Price</th>
                                        <th>Selling Price</th>
                                        <th>Stock</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="medicineRows">
                                    <tr>
                                        <td><input type="text" name="drug_name[]" class="form-control form-control-lg" required style="width:250px; padding:0.5rem;"></td>
                                        <td>
                                            <select name="category_id[]" class="form-select form-select-lg select2" required>
                                                <option value="">-- Select --</option>
                                                @foreach($categories as $id => $name)
                                                    <option value="{{ $id }}">{{ $name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td><input type="date" name="manufactured_date[]" class="form-control form-control-lg" required></td>
                                        <td><input type="date" name="expiry_date[]" class="form-control form-control-lg" required></td>
                                        <td><input type="number" step="0.01" name="price[]" class="form-control form-control-lg price" required style="width:100px; padding:0.5rem;"></td>
                                        <td><input type="number" step="0.01" name="selling_price[]" class="form-control form-control-lg selling_price" readonly style="width:100px; padding:0.5rem;"></td>
                                        <td><input type="number" name="stock[]" class="form-control form-control-lg" required></td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-danger btn-sm removeRow">-</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Add Row -->
                        <div class="d-flex justify-content-end mb-3">
                            <button type="button" class="btn btn-success btn-sm" id="addRow">Add Row +</button>
                        </div>

                        <!-- Save & Cancel -->
                        <div class="d-flex justify-content-end gap-2">
                            <button type="submit" class="btn btn-success">Save All Medicines</button>
                            <a href="{{ route('medicine.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>

                    </form>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $('.select2').select2({ width: '100%' });

    // Function to recalc selling price
    function recalcSellingPrice(row) {
        let price = parseFloat(row.find('.price').val()) || 0;
        row.find('.selling_price').val((price * 1.20).toFixed(2));
    }

    // Initial row
    $('#medicineRows tr').each(function() {
        let row = $(this);
        row.find('.price').on('input', function() {
            recalcSellingPrice(row);
        });
    });

    // Add new row
    $('#addRow').click(function() {
        let row = $(`
        <tr>
            <td><input type="text" name="drug_name[]" class="form-control form-control-lg" required></td>
            <td>
                <select name="category_id[]" class="form-select form-select-lg select2" required>
                    <option value="">-- Select --</option>
                    @foreach($categories as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </td>
            <td><input type="date" name="manufactured_date[]" class="form-control form-control-lg" required></td>
            <td><input type="date" name="expiry_date[]" class="form-control form-control-lg" required></td>
            <td><input type="number" step="0.01" name="price[]" class="form-control form-control-lg price" required></td>
            <td><input type="number" step="0.01" name="selling_price[]" class="form-control form-control-lg selling_price" readonly></td>
            <td><input type="number" name="stock[]" class="form-control form-control-lg" required></td>
            <td class="text-center"><button type="button" class="btn btn-danger btn-sm removeRow">-</button></td>
        </tr>`);
        $('#medicineRows').append(row);
        row.find('.select2').select2({ width: '100%' });

        // Bind input event for new row
        row.find('.price').on('input', function() {
            recalcSellingPrice(row);
        });
    });

    // Remove row
    $(document).on('click', '.removeRow', function() {
        $(this).closest('tr').remove();
    });
});
</script>
@endsection
