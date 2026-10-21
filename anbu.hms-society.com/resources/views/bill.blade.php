@extends('layout.admin')
@section('content')

<div class="main-wrapper">
    @include('include.header')
    @include('include.sidebar')

    <div class="page-wrapper">
        <div class="content container-fluid">

            <h3 class="text-success fw-bold mb-4">Discharge Bill Preview</h3>

            <div class="card shadow p-4 border-0 rounded-4">
                <h5 class="fw-bold text-primary mb-3">Document Viewer (.docx)</h5>

                <!-- ✅ Embed DOCX File in iframe -->
                <div class="ratio ratio-16x9 border rounded-3">
                    <iframe 
                        src="https://view.officeapps.live.com/op/embed.aspx?src={{ urlencode($fileUrl) }}" 
                        width="100%" 
                        height="700px" 
                        frameborder="0">
                    </iframe>
                </div>

                <div class="text-end mt-3">
                    <a href="{{ $fileUrl }}" class="btn btn-primary" download>
                        <i class="fas fa-download me-2"></i>Download .docx
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>

@endsection
