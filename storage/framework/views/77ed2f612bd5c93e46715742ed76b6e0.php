<?php $__env->startSection('title', 'Purchase ' . $purchase->invoice_no); ?>

<?php $__env->startPush('styles'); ?>
    <style>
        .invoice-card .invoice-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: #4f46e5;
            letter-spacing: 2px;
        }

        .invoice-card .label {
            font-size: .7rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #4f46e5;
            font-weight: 700;
            margin-bottom: .25rem;
        }

        .invoice-card .party-box {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: .9rem 1.1rem;
        }

        .invoice-card .items-table thead th {
            background: #4f46e5;
            color: #fff;
            border: none;
            font-size: .85rem;
        }

        .invoice-card .items-table tbody tr:nth-child(even) td {
            background: #f9fafb;
        }

        .invoice-card .grand-row td {
            background: #4f46e5 !important;
            color: #fff;
            font-weight: 700;
        }

        .invoice-card .due-row td {
            color: #ef4444;
            font-weight: 700;
        }

        [data-bs-theme="dark"] .invoice-card .party-box,
        [data-bs-theme="dark"] .invoice-card .items-table tbody tr:nth-child(even) td {
            background: #0f172a;
        }

        @media print {
            body {
                overflow: visible !important;
            }

            .sidebar,
            .topbar,
            .no-print {
                display: none !important;
            }

            .admin-wrapper,
            .main-content,
            .page-content {
                display: block !important;
                height: auto !important;
                overflow: visible !important;
            }

            .invoice-card {
                box-shadow: none !important;
            }

            .invoice-card .items-table thead th,
            .invoice-card .grand-row td {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <?php
        $siteSettings = $siteSettings ?? \App\Models\Setting::pluck('value', 'key');
        $phone = $purchase->branch->phone ?? ($siteSettings['phone'] ?? null);
        $email = $purchase->branch->email ?? ($siteSettings['email'] ?? null);
    ?>

    
    <div class="d-flex justify-content-between align-items-center mb-3 no-print">
        <h4 class="mb-0">Purchase <?php echo e($purchase->invoice_no); ?></h4>
        <div>
            <a href="<?php echo e(route('admin.purchases.pdf', $purchase)); ?>" class="btn btn-outline-primary"><i
                    class="bi bi-file-earmark-pdf"></i> Download PDF</a>
            <button class="btn btn-outline-secondary" onclick="window.print()"><i class="bi bi-printer"></i>
                Print</button>
        </div>
    </div>

    <div class="card print-area invoice-card">
        <div class="card-body p-4">

            
            <div class="row g-3 pb-3 mb-3 border-bottom border-2 border-primary">
                <div class="col-7">
                    <?php if(!empty($siteSettings['site_logo'] ?? null)): ?>
                        <img src="<?php echo e(Storage::url($siteSettings['site_logo'])); ?>" style="height:55px" alt="Logo">
                    <?php else: ?>
                        <div class="fs-3 fw-bold text-primary"><?php echo e($siteSettings['site_name'] ?? config('app.name')); ?>

                        </div>
                    <?php endif; ?>

                    <div class="text-muted small mt-2" style="line-height:1.7">
                        <?php if($purchase->branch?->name): ?>
                            <b class="text-body"><?php echo e($purchase->branch->name); ?></b><br>
                        <?php endif; ?>
                        <?php if($purchase->branch?->address): ?>
                            <?php echo e($purchase->branch->address); ?><br>
                        <?php endif; ?>
                        <?php if($phone): ?>
                            Phone: <?php echo e($phone); ?><br>
                        <?php endif; ?>
                        <?php if($email): ?>
                            Email: <?php echo e($email); ?>

                        <?php endif; ?>
                    </div>
                </div>

                <div class="col-5 text-end">
                    <div class="invoice-title">PURCHASE</div>
                    <div class="text-muted small mt-1" style="line-height:1.7">
                        <b>No:</b> <?php echo e($purchase->invoice_no); ?><br>
                        <b>Date:</b> <?php echo e($purchase->purchase_date->format('d M Y')); ?>

                    </div>
                    <div class="mt-2">
                        <span
                            class="badge bg-<?php echo e($purchase->payment_status === 'paid' ? 'success' : 'danger'); ?>"><?php echo e(strtoupper($purchase->payment_status)); ?></span>
                    </div>
                </div>
            </div>

            
            <div class="party-box mb-4">
                <div class="label">Supplier</div>
                <div class="text-muted small" style="line-height:1.7">
                    <b class="text-body"><?php echo e($purchase->supplier->name ?? '-'); ?></b><br>
                    <?php if($purchase->supplier?->phone): ?>
                        Phone: <?php echo e($purchase->supplier->phone); ?><br>
                    <?php endif; ?>
                    <?php if($purchase->supplier?->email): ?>
                        Email: <?php echo e($purchase->supplier->email); ?><br>
                    <?php endif; ?>
                    <?php if($purchase->supplier?->address): ?>
                        <?php echo e($purchase->supplier->address); ?>

                    <?php endif; ?>
                </div>
            </div>

            
            <div class="table-responsive">
                <table class="table align-middle items-table">
                    <thead>
                        <tr>
                            <th style="width:5%">#</th>
                            <th>Product</th>
                            <th>Batch</th>
                            <th class="text-center">Qty</th>
                            <th class="text-end">Purchase Price</th>
                            <th class="text-center">Expiry</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $purchase->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($i + 1); ?></td>
                                <td><?php echo e($item->product->name ?? 'Deleted product'); ?></td>
                                <td><?php echo e($item->batch_no ?? '-'); ?></td>
                                <td class="text-center">
                                    <?php echo e($item->unit_qty ?? $item->quantity); ?>

                                    <?php echo e($item->productUnit->unit->name ?? 'pcs'); ?>

                                    <?php if($item->unit_qty && $item->productUnit?->conversion_factor > 1): ?>
                                        <br><small class="text-muted">(<?php echo e($item->quantity); ?> pcs total)</small>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">৳<?php echo e(number_format($item->purchase_price, 2)); ?></td>
                                <td class="text-center"><?php echo e($item->expiry_date?->format('d M Y') ?? '-'); ?></td>
                                <td class="text-end">৳<?php echo e(number_format($item->subtotal, 2)); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>

            
            <div class="row justify-content-end">
                <div class="col-md-5 col-lg-4">
                    <table class="table table-sm mb-0">
                        <tr>
                            <td>Subtotal</td>
                            <td class="text-end">৳<?php echo e(number_format($purchase->total_amount, 2)); ?></td>
                        </tr>
                        <tr>
                            <td>Discount</td>
                            <td class="text-end">- ৳<?php echo e(number_format($purchase->discount, 2)); ?></td>
                        </tr>
                        <tr>
                            <td>Tax</td>
                            <td class="text-end">৳<?php echo e(number_format($purchase->tax, 2)); ?></td>
                        </tr>
                        <?php if($purchase->shipping_cost > 0): ?>
                            <tr>
                                <td>Shipping</td>
                                <td class="text-end">৳<?php echo e(number_format($purchase->shipping_cost, 2)); ?></td>
                            </tr>
                        <?php endif; ?>
                        <tr class="grand-row">
                            <td>Grand Total</td>
                            <td class="text-end">৳<?php echo e(number_format($purchase->grand_total, 2)); ?></td>
                        </tr>
                        <tr>
                            <td>Paid</td>
                            <td class="text-end">৳<?php echo e(number_format($purchase->paid_amount, 2)); ?></td>
                        </tr>
                        <tr class="due-row">
                            <td>Due</td>
                            <td class="text-end">৳<?php echo e(number_format($purchase->due_amount, 2)); ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            
            <div class="text-center mt-4">
                <?php echo \App\Services\BarcodeService::svg($purchase->invoice_no); ?>

                <div class="text-muted small">Purchase reference — <?php echo e($purchase->invoice_no); ?></div>
            </div>

            <div class="text-center text-muted small border-top pt-2 mt-4">
                This is a computer-generated purchase invoice from
                <?php echo e($siteSettings['site_name'] ?? config('app.name')); ?>.
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\pharmacy\resources\views/admin/purchases/show.blade.php ENDPATH**/ ?>