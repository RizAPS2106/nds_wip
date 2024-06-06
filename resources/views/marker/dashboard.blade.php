<h5 class="fw-bold mb-3">Dashboard</h5>

{{-- <div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between">
            <h5 class="text-sb fw-bold">DC Input Count</h5>
            <div class="d-flex justify-content-end gap-1 mb-3">
                <select class="form-select form-select-sm select2bs4 w-auto" id="dcqty-month-filter" readonly value="{{ date('m') }}">
                    <option value="" selected disabled>Bulan</option>
                    @foreach ($months as $month)
                        <option value="{{ $month['angka'] }}">{{ $month['nama'] }}</option>
                    @endforeach
                </select>
                <select class="form-select form-select-sm select2bs4 w-auto" id="dcqty-year-filter" readonly value="{{ date('Y') }}">
                    <option value="" selected disabled>Tahun</option>
                    @foreach ($years as $year)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="d-none mb-3" id="loading-dc-qty">
            <div class="loading-container">
                <div class="loading"></div>
            </div>
        </div>
        <div class="row d-none" id="dc-qty-data">
            <div class="col-6 col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-sb"><i class="fa fa-ticket"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Stocker</span>
                        <span class="info-box-number" id="stocker-qty"></span>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-sb-secondary"><i class="fas fa-location-arrow"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Secondary</span>
                        <span class="info-box-number" id="secondary-qty"></span>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-sb"><i class="fa-solid fa-memory"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Non-Secondary</span>
                        <span class="info-box-number" id="non-secondary-qty"></span>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-sb-secondary"><i class="fa-solid fa-users-line"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Line</span>
                        <span class="info-box-number" id="line-qty"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> --}}
<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between">
            <h5 class="text-sb fw-bold">Marker Summary</h5>
            <div class="d-flex justify-content-start align-items-center gap-1 mb-3">
                <div class="mb-1">
                    <select class="form-select form-select-sm select2bs4" id="marker-month-filter" readonly value="{{ date('m') }}">
                        <option value="" selected disabled>Bulan</option>
                        @foreach ($months as $month)
                            <option value="{{ $month['angka'] }}">{{ $month['nama'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-1">
                    <select class="form-select form-select-sm select2bs4" id="marker-year-filter" readonly value="{{ date('Y') }}">
                        <option value="" selected disabled>Tahun</option>
                        @foreach ($years as $year)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-sm" id="datatable-marker">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Buyer</th>
                        <th>No. WS</th>
                        <th>Style</th>
                        <th>Color</th>
                        <th>No. Marker</th>
                        <th>Urutan</th>
                        <th>Panel</th>
                        <th>Part</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>
