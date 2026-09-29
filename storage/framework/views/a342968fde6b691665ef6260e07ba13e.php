<?php $__env->startSection("title", "Profit & Loss"); ?>
<?php $__env->startSection("content"); ?>
<h4 class="mb-3">Profit &amp; Loss Statement</h4>
<?php echo $__env->make("admin.reports._range_filter", array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="card mb-3">
    <div class="card-body">
        <table class="table table-borderless">
            <tr><td>Total Revenue (Sales)</td><td class="text-end fw-bold">৳<?php echo e(number_format($revenue,2)); ?></td></tr>
            <tr><td>Cost of Goods Sold (COGS)</td><td class="text-end text-danger">- ৳<?php echo e(number_format($cogs,2)); ?></td></tr>
            <tr class="border-top"><td class="fw-bold">Gross Profit</td><td class="text-end fw-bold">৳<?php echo e(number_format($grossProfit,2)); ?></td></tr>
            <tr><td>Operating Expenses</td><td class="text-end text-danger">- ৳<?php echo e(number_format($expenseTotal,2)); ?></td></tr>
            <tr class="border-top fs-5"><td class="fw-bold">Net Profit / Loss</td>
                <td class="text-end fw-bold <?php echo e($netProfit >= 0 ? 'text-success' : 'text-danger'); ?>">
                    ৳<?php echo e(number_format($netProfit,2)); ?> <?php echo e($netProfit >= 0 ? "(Profit)" : "(Loss)"); ?>

                </td>
            </tr>
        </table>
    </div>
</div>

<div class="card"><div class="card-header fw-bold">Monthly Revenue vs Gross Profit</div>
<div class="card-body"><canvas id="plChart" height="100"></canvas></div></div>
<?php $__env->stopSection(); ?>
<?php $__env->startPush("scripts"); ?>
<script>
new Chart(document.getElementById("plChart"), {
    type: "bar",
    data: {
        labels: <?php echo json_encode($monthly->pluck("ym")); ?>,
        datasets: [
            { label: "Revenue", data: <?php echo json_encode($monthly->pluck("revenue")); ?>, backgroundColor: "#4f46e5" },
            { label: "Gross Profit", data: <?php echo json_encode($monthly->pluck("gross_profit")); ?>, backgroundColor: "#10b981" },
        ]
    },
    options: { responsive: true, plugins:{legend:{position:"bottom"}} }
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make("layouts.admin", array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\pharmacy\resources\views/admin/reports/profit-loss.blade.php ENDPATH**/ ?>