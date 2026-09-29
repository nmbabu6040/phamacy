<?php $__env->startSection("title", "Expenses"); ?>
<?php $__env->startSection("content"); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Expenses <span class="text-danger">(Total: ৳<?php echo e(number_format($totalExpense,2)); ?>)</span></h4>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal"><i class="bi bi-plus-lg"></i> Add Expense</button>
</div>
<div class="card mb-3"><div class="card-body">
    <form class="row g-2" method="GET">
        <div class="col-md-3"><input type="date" name="from" value="<?php echo e(request('from')); ?>" class="form-control"></div>
        <div class="col-md-3"><input type="date" name="to" value="<?php echo e(request('to')); ?>" class="form-control"></div>
        <div class="col-md-3"><select name="category_id" class="form-select"><option value="">All Categories</option>
            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($c->id); ?>" <?php if(request('category_id')==$c->id): echo 'selected'; endif; ?>><?php echo e($c->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select></div>
        <div class="col-md-3"><button class="btn btn-outline-primary w-100">Filter</button></div>
    </form>
</div></div>
<div class="card"><div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light"><tr><th>Title</th><th>Category</th><th>Amount</th><th>Date</th><th>Actions</th></tr></thead>
        <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $expenses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr><td><?php echo e($row->title); ?></td><td><?php echo e($row->category->name ?? '-'); ?></td><td>৳<?php echo e(number_format($row->amount,2)); ?></td><td><?php echo e($row->expense_date->format('d M Y')); ?></td>
            <td><form action="<?php echo e(route('admin.expenses.destroy', $row)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Delete?')"><?php echo csrf_field(); ?> <?php echo method_field("DELETE"); ?><button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form></td></tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="5" class="text-center text-muted py-4">No expenses found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table></div>
    <div class="card-footer"><?php echo e($expenses->links()); ?></div>
</div>
<div class="modal fade" id="addModal"><div class="modal-dialog"><div class="modal-content">
    <form action="<?php echo e(route('admin.expenses.store')); ?>" method="POST" enctype="multipart/form-data"><?php echo csrf_field(); ?>
    <div class="modal-header"><h6 class="modal-title">Add Expense</h6><button class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
        <label class="form-label">Title</label><input name="title" class="form-control mb-2" required>
        <label class="form-label">Category</label><select name="expense_category_id" class="form-select mb-2" required>
            <option value="">-- Select --</option><?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($c->id); ?>"><?php echo e($c->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <label class="form-label">Amount</label><input type="number" step="0.01" name="amount" class="form-control mb-2" required>
        <label class="form-label">Date</label><input type="date" name="expense_date" class="form-control mb-2" value="<?php echo e(date('Y-m-d')); ?>" required>
        <label class="form-label">Attachment</label><input type="file" name="attachment" class="form-control mb-2">
        <label class="form-label">Note</label><textarea name="note" class="form-control"></textarea>
    </div>
    <div class="modal-footer"><button class="btn btn-primary">Save</button></div></form>
</div></div></div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make("layouts.admin", array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\pharmacy\resources\views/admin/expenses/index.blade.php ENDPATH**/ ?>