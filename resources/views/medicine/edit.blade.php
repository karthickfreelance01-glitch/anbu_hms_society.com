@extends('layout.admin')
@section('content')
<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')
    <div class="page-wrapper">
        <div class="content pb-0">
            <div class="container-fluid">

                <h3 class="text-primary fw-bold mb-4">Edit Medicine</h3>

                <div class="card shadow-lg rounded-4 p-4">
                    <form action="{{ route('medicine.update', $medicine->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Drug Name</label>
                                <input type="text" name="drug_name" class="form-control" value="{{ $medicine->drug_name }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Category</label>
                                <select name="category_id" class="form-select select2" required>
                                    <option value="">-- Select Category --</option>
                                    @foreach($categories as $id => $name)
                                        <option value="{{ $id }}" {{ $medicine->category_id == $id ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Manufactured Date</label>
                                <input type="date" name="manufactured_date" class="form-control" value="{{ $medicine->manufactured_date }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Expiry Date</label>
                                <input type="date" name="expiry_date" class="form-control" value="{{ $medicine->expiry_date }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Price (₹)</label>
                                <input type="number" step="0.01" name="price" id="price" class="form-control" value="{{ $medicine->price }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Selling Price (₹)</label>
                                <input type="number" step="0.01" name="selling_price" id="selling_price" class="form-control" value="{{ $medicine->selling_price }}" readonly>
                            </div>

                            <div class="col-12 mt-3">
                                <button type="submit" class="btn btn-primary">Update Medicine</button>
                                <a href="{{ route('medicine.index') }}" class="btn btn-secondary">Cancel</a>
                            </div>
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
    $('.select2').select2({ width: '100%' });

    $('#price').on('input', function() {
        let price = parseFloat($(this).val()) || 0;
        $('#selling_price').val((price * 1.25).toFixed(2));
    });
</script>
@endsection
