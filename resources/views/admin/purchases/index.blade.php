@extends('layouts.admin')
@section('title', 'Purchases')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Purchases</h4>
        <a href="{{ route('admin.purchases.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> New Purchase</a>
    </div>
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Invoice</th>
                        <th>Supplier</th>
                        <th>Date</th>
                        <th>Total</th>
                        <th>Paid</th>
                        <th>Due</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchases as $p)
                        <tr>
                            <td><a href="{{ route('admin.purchases.show', $p) }}">{{ $p->invoice_no }}</a></td>
                            <td>{{ $p->supplier->name ?? '-' }}</td>
                            <td>{{ $p->purchase_date->format('d M Y') }}</td>
                            <td>৳{{ number_format($p->grand_total, 2) }}</td>
                            <td>৳{{ number_format($p->paid_amount, 2) }}</td>
                            <td>৳{{ number_format($p->due_amount, 2) }}</td>
                            <td>
                                @if ($p->status === 'cancelled')
                                    <span class="badge bg-danger">Returned</span>
                                @else
                                    <span
                                        class="badge bg-{{ $p->payment_status === 'paid' ? 'success' : 'warning' }}">{{ ucfirst($p->payment_status) }}</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.purchases.show', $p) }}"
                                    class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i></a>
                                @if ($p->status !== 'cancelled')
                                    <form action="{{ route('admin.purchases.destroy', $p) }}" method="POST"
                                        class="d-inline"
                                        onsubmit="return confirm('Return this purchase to the supplier? Unsold stock will be reversed.')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" title="Return to supplier"><i
                                                class="bi bi-arrow-return-left"></i></button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">No purchases found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $purchases->links() }}</div>
    </div>
@endsection
