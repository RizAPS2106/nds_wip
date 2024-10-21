@extends('layouts.index')

@section('custom-link')
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
@endsection

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="mx-3 my-3">
                <h5 class="card-title fw-bold text-sb text-center"><i class="fa-solid fa-file"></i> Order Cutting Output</h5>
            </div>
            <div class="card-body">
                @livewire('track-cutting-output')
            </div>
        </div>
    </div>
@endsection

@section('custom-script')
    <script src="{{ asset('plugins/select2/js/select2.full.min.js') }}"></script>
    <script>
        $('.select2').select2({
            theme: 'bootstrap4',
        })

        $('.select2bs4').select2({
            theme: 'bootstrap4',
        })
    </script>
    <script>
        Livewire.on('loadingStart', () => {
            if (document.getElementById('loadingOrderOutput')) {
                $('#loadingOrderOutput').removeClass('hidden');
            }
        });
    </script>
@endsection
