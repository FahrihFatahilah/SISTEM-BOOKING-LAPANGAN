@extends('admin.layouts.app')

@section('title', 'Pindahkan Stok')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-bold text-dark">Pindahkan Stok</h2>
                <p class="text-muted">Gudang → Display</p>
            </div>
            <a href="{{ route('admin.stock-transfers.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i> Kembali
            </a>
        </div>
    </div>
</div>

<form method="POST" action="{{ route('admin.stock-transfers.store') }}" id="transferForm">
    @csrf
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-transparent">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-box-arrow-right me-2"></i>
                        Daftar Barang
                    </h5>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            @foreach($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    <div id="itemRows">
                        <div class="row g-3 mb-3 item-row align-items-end">
                            <div class="col-md-6">
                                <label class="form-label">Produk</label>
                                <select class="form-select product-select" name="items[0][product_id]" required>
                                    <option value="">Pilih Produk</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" data-warehouse="{{ $product->warehouse_stock }}">
                                            {{ $product->name }} (Gudang: {{ $product->warehouse_stock }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Jumlah</label>
                                <input type="number" class="form-control qty-input" name="items[0][quantity]" min="1" required>
                            </div>
                            <div class="col-md-3">
                                <button type="button" class="btn btn-danger btn-remove" style="display:none;">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <button type="button" class="btn btn-outline-primary btn-sm" id="addRow">
                        <i class="bi bi-plus-circle me-1"></i> Tambah Barang
                    </button>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-calendar me-2"></i> Info Transfer
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Tanggal Pemindahan <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="transfer_date" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catatan</label>
                        <textarea class="form-control" name="notes" rows="3" placeholder="Opsional"></textarea>
                    </div>

                    <div id="transferSummary" class="mb-3" style="display:none;">
                        <hr>
                        <h6 class="text-muted">Ringkasan</h6>
                        <div id="summaryContent"></div>
                    </div>

                    <button type="submit" class="btn btn-success w-100">
                        <i class="bi bi-check-circle me-2"></i> Proses Pemindahan
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
let rowIndex = 1;

document.getElementById('addRow').addEventListener('click', function() {
    const template = `
        <div class="row g-3 mb-3 item-row align-items-end">
            <div class="col-md-6">
                <select class="form-select product-select" name="items[${rowIndex}][product_id]" required>
                    <option value="">Pilih Produk</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" data-warehouse="{{ $product->warehouse_stock }}">
                            {{ $product->name }} (Gudang: {{ $product->warehouse_stock }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <input type="number" class="form-control qty-input" name="items[${rowIndex}][quantity]" min="1" required>
            </div>
            <div class="col-md-3">
                <button type="button" class="btn btn-danger btn-remove">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        </div>
    `;
    document.getElementById('itemRows').insertAdjacentHTML('beforeend', template);
    rowIndex++;
    updateRemoveButtons();
});

document.getElementById('itemRows').addEventListener('click', function(e) {
    if (e.target.closest('.btn-remove')) {
        e.target.closest('.item-row').remove();
        updateRemoveButtons();
        updateSummary();
    }
});

document.getElementById('itemRows').addEventListener('change', updateSummary);
document.getElementById('itemRows').addEventListener('input', updateSummary);

function updateRemoveButtons() {
    const rows = document.querySelectorAll('.item-row');
    rows.forEach((row, i) => {
        const btn = row.querySelector('.btn-remove');
        btn.style.display = rows.length > 1 ? 'inline-block' : 'none';
    });
}

function updateSummary() {
    const rows = document.querySelectorAll('.item-row');
    let html = '';
    let hasItems = false;

    rows.forEach(row => {
        const select = row.querySelector('.product-select');
        const qty = row.querySelector('.qty-input');
        if (select.value && qty.value) {
            hasItems = true;
            const name = select.options[select.selectedIndex].text.split(' (')[0];
            const warehouse = select.options[select.selectedIndex].dataset.warehouse;
            const qtyVal = parseInt(qty.value);
            const isOver = qtyVal > parseInt(warehouse);
            html += `<div class="d-flex justify-content-between mb-1">
                <small>${name}</small>
                <small class="${isOver ? 'text-danger fw-bold' : ''}">${qtyVal} pcs</small>
            </div>`;
            if (isOver) {
                html += `<small class="text-danger">⚠ Melebihi stok gudang (${warehouse})</small>`;
            }
        }
    });

    const summary = document.getElementById('transferSummary');
    const content = document.getElementById('summaryContent');
    if (hasItems) {
        summary.style.display = 'block';
        content.innerHTML = html;
    } else {
        summary.style.display = 'none';
    }
}
</script>
@endpush
