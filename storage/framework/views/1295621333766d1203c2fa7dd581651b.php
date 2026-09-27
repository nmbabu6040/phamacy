<?php $__env->startSection('title', 'About Us | ' . ($siteSettings['site_name'] ?? config('app.name'))); ?>
<?php $__env->startSection('content'); ?>
    <div class="page-banner">
        <div class="container">
            <h2 data-aos="fade-up">About Us</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?php echo e(route('home')); ?>">Home</a></li>
                    <li class="breadcrumb-item active">About</li>
                </ol>
            </nav>
        </div>
    </div>

    <section class="py-5">
        <div class="container">
            <div class="row g-5 align-items-center mb-5">
                <div class="col-lg-6" data-aos="fade-right">
                    <img src="<?php echo e(!empty($siteSettings['about_image']) ? Storage::url($siteSettings['about_image']) : 'https://images.unsplash.com/photo-1576091160399-112ba8d25d1f?w=800'); ?>"
                        class="img-fluid rounded-4 shadow">
                </div>
                <div class="col-lg-6" data-aos="fade-left">
                    <span
                        class="badge bg-primary-subtle text-primary mb-2"><?php echo e($siteSettings['about_badge'] ?? 'About ' . ($siteSettings['site_name'] ?? config('app.name'))); ?></span>
                    <h2 class="mb-3"><?php echo e($siteSettings['about_heading'] ?? 'Committed to Your Health & Wellbeing'); ?></h2>
                    <p><?php echo e($siteSettings['about_description'] ?? 'We are a full-service pharmacy providing genuine medicines, expert pharmacist consultation, and fast home delivery.'); ?>

                    </p>
                    <div class="row g-3 mt-2">
                        <?php $__currentLoopData = $aboutFeatures; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="col-6"><i class="bi bi-check-circle-fill text-primary"></i> <?php echo e($f->text); ?>

                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>

            <div class="row text-center g-4">
                <?php $__currentLoopData = $aboutValues; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-md-4" data-aos="fade-up">
                        <div class="value-card"><i class="bi <?php echo e($v->icon); ?>"></i>
                            <h5><?php echo e($v->title); ?></h5>
                            <p><?php echo e($v->description); ?></p>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.frontend', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\pharmacy\resources\views/frontend/pages/about.blade.php ENDPATH**/ ?>