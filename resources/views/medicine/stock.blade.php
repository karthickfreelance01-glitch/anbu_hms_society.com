@extends('layout.admin')
@section('content')
<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')
    <div class="page-wrapper">
        <div class="content pb-0">
            <div class="container-fluid">

                <h3 class="text-primary fw-bold mb-4">Re-enter Stock</h3>

                <div class="card shadow-lg rounded-4 p-4">
                    <form action="{{ route('medicine.stock.update', $medicine->id) }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Drug Name</label>
                                <input type="text" class="form-control" value="{{ $medicine->drug_name }}" readonly>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Current Stock</label>
                                <input type="number" class="form-control" value="{{ $medicine->current_stock }}" readonly>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Add Stock</label>
                                <input type="number" name="stock" class="form-control" value="0" required>
                            </div>

                            <div class="col-12 mt-3">
                                <button type="submit" class="btn btn-success">Update Stock</button>
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
