@extends('admin.layouts.app')

@push('styles')
<style>
.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.15) !important;
    border-color: #2563eb !important;
}
.product-card:active {
    transform: translateY(-2px);
}
#cartItems:empty::before {
    content: 'Keranjang kosong';
    display: block;
    text-align: center;
    color: #6c757d;
    padding: 20px;
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Produk</h5>
                        <a href="{{ route('admin.pos.sales') }}" class="btn btn-sm btn-info">
                            <i class="bi bi-clock-history"></i> Riwayat
                        </a>
                    </div>
                    @if($branches)
                    <div class="mt-2">
                        <select id="branchSelect" class="form-select form-select-sm" onchange="window.location='{{ route('admin.pos.index') }}?branch_id='+this.value">
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ $selectedBranch == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    <input type="text" id="searchProduct" class="form-control mt-2" placeholder="Cari produk...">
                </div>
                <div class="card-body" style="max-height: 600px; overflow-y: auto;">
                    <div class="row" id="productList">
                        @foreach($products as $product)
                        <div class="col-md-3 mb-3 product-item" data-name="{{ strtolower($product->name) }}">
                            <div class="card h-100 product-card" onclick="addToCart({{ $product->id }}, '{{ $product->name }}', {{ $product->selling_price }}, {{ $product->display_stock }})" style="cursor: pointer; transition: all 0.3s;">
                                <div class="card-body text-center">
                                    <div class="mb-2">
                                        <i class="bi bi-box-seam" style="font-size: 2rem; color: #6c757d;"></i>
                                    </div>
                                    <h6 class="mb-1">{{ $product->name }}</h6>
                                    <p class="text-muted mb-1" style="font-size: 0.85rem;">{{ $product->code }}</p>
                                    <p class="text-success fw-bold mb-1">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</p>
                                    <small class="badge {{ $product->display_stock <= $product->min_stock ? 'bg-warning' : 'bg-secondary' }}">Stok: {{ $product->display_stock }}</small>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h5>Keranjang</h5>
                    <button class="btn btn-sm btn-danger" onclick="clearCart()">Clear</button>
                </div>
                <div class="card-body">
                    <div id="cartItems" style="max-height: 300px; overflow-y: auto;"></div>
                    
                    <hr>
                    
                    <div class="mb-2">
                        <label>Subtotal</label>
                        <input type="text" class="form-control" id="subtotal" readonly>
                    </div>
                    <div class="mb-2">
                        <label>Diskon</label>
                        <input type="number" class="form-control" id="discount" value="0" onchange="calculateTotal()">
                    </div>
                    <div class="mb-2">
                        <label>Pajak (%)</label>
                        <input type="number" class="form-control" id="tax" value="0" onchange="calculateTotal()">
                    </div>
                    <div class="mb-2">
                        <label class="fw-bold">Total</label>
                        <input type="text" class="form-control fw-bold" id="total" readonly>
                    </div>
                    <div class="mb-2">
                        <label>Bayar</label>
                        <input type="number" class="form-control" id="paid" onchange="calculateChange()">
                    </div>
                    <div class="mb-2">
                        <label>Kembali</label>
                        <input type="text" class="form-control" id="change" readonly>
                    </div>
                    <div class="mb-2">
                        <label>Metode Pembayaran</label>
                        <select class="form-control" id="payment_method">
                            <option value="cash">Cash</option>
                            <option value="card">Card</option>
                            <option value="transfer">Transfer</option>
                        </select>
                    </div>
                    
                    <button class="btn btn-primary w-100 mt-3" onclick="processPayment()">
                        <i class="bi bi-check-circle"></i> Proses Pembayaran (F9)
                    </button>
                    
                    <div class="mt-3 p-2 bg-light rounded">
                        <small class="text-muted">
                            <strong>Shortcuts:</strong><br>
                            F2 = Cari Produk | F9 = Bayar | ESC = Clear
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let cart = [];

function addToCart(id, name, price, stock) {
    const existing = cart.find(item => item.product_id === id);
    if (existing) {
        if (existing.quantity < stock) {
            existing.quantity++;
        } else {
            alert('Stok tidak mencukupi');
            return;
        }
    } else {
        cart.push({ product_id: id, name, price, quantity: 1, stock });
    }
    // Visual feedback
    const productCard = event.currentTarget;
    productCard.style.transform = 'scale(0.95)';
    setTimeout(() => {
        productCard.style.transform = '';
    }, 100);
    renderCart();
}

function removeFromCart(id) {
    cart = cart.filter(item => item.product_id !== id);
    renderCart();
}

function updateQuantity(id, quantity) {
    const item = cart.find(item => item.product_id === id);
    if (item) {
        if (quantity > item.stock) {
            alert('Stok tidak mencukupi');
            return;
        }
        item.quantity = parseInt(quantity);
        if (item.quantity <= 0) removeFromCart(id);
        else renderCart();
    }
}

function renderCart() {
    const cartDiv = document.getElementById('cartItems');
    if (cart.length === 0) {
        cartDiv.innerHTML = '<div class="text-center text-muted py-4"><i class="bi bi-cart-x" style="font-size: 2rem;"></i><p class="mt-2">Keranjang kosong</p></div>';
    } else {
        cartDiv.innerHTML = cart.map(item => `
            <div class="d-flex justify-content-between align-items-center mb-2 border-bottom pb-2">
                <div style="flex: 1;">
                    <strong>${item.name}</strong><br>
                    <small class="text-muted">Rp ${item.price.toLocaleString('id-ID')}</small>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <input type="number" class="form-control form-control-sm" style="width: 60px;" 
                           value="${item.quantity}" min="1" max="${item.stock}"
                           onchange="updateQuantity(${item.product_id}, this.value)">
                    <button class="btn btn-sm btn-danger" onclick="removeFromCart(${item.product_id})" title="Hapus">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        `).join('');
    }
    calculateTotal();
}

function calculateTotal() {
    const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    const discount = parseFloat(document.getElementById('discount').value) || 0;
    const taxPercent = parseFloat(document.getElementById('tax').value) || 0;
    const tax = (subtotal - discount) * (taxPercent / 100);
    const total = subtotal - discount + tax;
    
    document.getElementById('subtotal').value = 'Rp ' + subtotal.toLocaleString('id-ID');
    document.getElementById('total').value = 'Rp ' + total.toLocaleString('id-ID');
}

function calculateChange() {
    const total = parseFloat(document.getElementById('total').value.replace(/[^0-9]/g, ''));
    const paid = parseFloat(document.getElementById('paid').value) || 0;
    const change = paid - total;
    document.getElementById('change').value = 'Rp ' + (change > 0 ? change : 0).toLocaleString('id-ID');
}

function clearCart() {
    cart = [];
    renderCart();
    document.getElementById('paid').value = '';
    document.getElementById('change').value = '';
}

function processPayment() {
    if (cart.length === 0) {
        alert('Keranjang kosong');
        return;
    }
    
    const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    const discount = parseFloat(document.getElementById('discount').value) || 0;
    const taxPercent = parseFloat(document.getElementById('tax').value) || 0;
    const tax = (subtotal - discount) * (taxPercent / 100);
    const total = subtotal - discount + tax;
    const paid = parseFloat(document.getElementById('paid').value) || 0;
    
    if (paid < total) {
        alert('Pembayaran kurang');
        return;
    }
    
    fetch('{{ route("admin.pos.store") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            items: cart,
            subtotal: subtotal,
            tax: tax,
            discount: discount,
            total: total,
            paid: paid,
            payment_method: document.getElementById('payment_method').value
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert('Transaksi berhasil! Invoice: ' + data.invoice_number);
            window.open('{{ url("admin/pos/print") }}/' + data.sale_id, '_blank');
            clearCart();
        } else {
            alert('Error: ' + data.message);
        }
    });
}

document.getElementById('searchProduct').addEventListener('input', function(e) {
    const search = e.target.value.toLowerCase();
    document.querySelectorAll('.product-item').forEach(item => {
        const name = item.dataset.name;
        item.style.display = name.includes(search) ? '' : 'none';
    });
});

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // F2 - Focus search
    if (e.key === 'F2') {
        e.preventDefault();
        document.getElementById('searchProduct').focus();
    }
    // F9 - Process payment
    if (e.key === 'F9') {
        e.preventDefault();
        processPayment();
    }
    // ESC - Clear cart
    if (e.key === 'Escape' && cart.length > 0) {
        if (confirm('Kosongkan keranjang?')) {
            clearCart();
        }
    }
});

// Auto focus on paid input when total changes
let lastTotal = 0;
setInterval(() => {
    const total = parseFloat(document.getElementById('total').value.replace(/[^0-9]/g, ''));
    if (total > 0 && total !== lastTotal) {
        lastTotal = total;
        document.getElementById('paid').focus();
    }
}, 500);
</script>
@endsection
