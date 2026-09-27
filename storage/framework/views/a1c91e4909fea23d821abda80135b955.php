<?php $__env->startSection('title', ($siteSettings['meta_title'] ?? config('app.name')) . ' - Home'); ?>

<?php $__env->startSection('content'); ?>

    <!-- Hero Slider -->
    <section class="hero-slider">
        <div class="swiper heroSwiper">
            <div class="swiper-wrapper">
                <?php $__empty_1 = true; $__currentLoopData = $sliders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $slide): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="swiper-slide"
                        style="background-image:linear-gradient(120deg, rgba(17,24,39,.75), rgba(79,70,229,.55)), url(<?php echo e($slide->image_url); ?>)">
                        <div class="container h-100 d-flex align-items-center">
                            <div class="hero-content text-white" data-aos="fade-up">
                                <h1 class="display-4 fw-bold"><?php echo e($slide->title); ?></h1>
                                <p class="lead"><?php echo e($slide->subtitle); ?></p>
                                <?php if($slide->button_text): ?>
                                    <a href="<?php echo e($slide->button_link ?? '#'); ?>"
                                        class="btn btn-primary btn-lg mt-2"><?php echo e($slide->button_text); ?></a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="swiper-slide" style="background:linear-gradient(120deg,#4f46e5,#06b6d4)">
                        <div class="container h-100 d-flex align-items-center">
                            <div class="hero-content text-white" data-aos="fade-up">
                                <h1 class="display-4 fw-bold">Your Health, Our Priority</h1>
                                <p class="lead">Genuine medicines delivered to your doorstep</p>
                                <a href="<?php echo e(route('shop.index')); ?>" class="btn btn-primary btn-lg mt-2">Shop Now</a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <div class="swiper-pagination"></div>
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
        </div>
    </section>

    <!-- Counters (parallax strip) -->
    <section class="counter-parallax" data-aos="fade-in">
        <div class="container">
            <div class="row text-center text-white g-4">

                <?php $__currentLoopData = $counters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $counter): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-6 col-md-3">
                        <div class="counter-icon"><i class="bi <?php echo e($counter->icon); ?>"></i></div>
                        <h2 class="counter" data-count="<?php echo e($counter->count); ?>">0</h2>
                        <p><?php echo e($counter->label); ?></p>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            </div>
        </div>
    </section>

    <!-- Category grid (Isotope) -->
    <section class="py-5">
        <div class="container">
            <div class="section-title text-center mb-4" data-aos="fade-up">
                <span class="badge bg-primary-subtle text-primary mb-2">Browse</span>
                <h2>Shop by Category</h2>
            </div>
            <div class="row isotope-grid g-4">
                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-lg-3 col-md-4 col-6 isotope-item" data-aos="zoom-in"
                        data-aos-delay="<?php echo e($loop->index * 50); ?>">
                        <a href="<?php echo e(route('shop.index', ['category' => $cat->slug])); ?>" class="category-card">
                            <?php if(!$cat->image): ?>
                                <i class="bi bi-capsule-pill"></i>
                            <?php else: ?>
                                <img src="<?php echo e($cat->image_url); ?>" alt="<?php echo e($cat->name); ?>" class="img-fluid"
                                    width="80">
                            <?php endif; ?>

                            <h6><?php echo e($cat->name); ?></h6>
                            <span><?php echo e($cat->products_count); ?> items</span>
                        </a>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </section>

    <!-- Featured products -->
    <section class="py-5 bg-light-soft">
        <div class="container">
            <div class="section-title text-center mb-4" data-aos="fade-up">
                <span class="badge bg-primary-subtle text-primary mb-2">Featured</span>
                <h2>Popular Medicines</h2>
            </div>
            <div class="row g-4">
                <?php $__empty_1 = true; $__currentLoopData = $featuredProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="<?php echo e($loop->index * 60); ?>">
                        <div class="product-card">
                            <div class="product-thumb">
                                <img src="<?php echo e($p->image ? asset('storage/' . $p->image) : 'https://via.placeholder.com/300x220?text=' . urlencode($p->name)); ?>"
                                    alt="<?php echo e($p->name); ?>">
                                <a href="<?php echo e($p->image ? asset('storage/' . $p->image) : 'https://via.placeholder.com/600'); ?>"
                                    class="quick-view venobox" data-gall="products" title="<?php echo e($p->name); ?>"><i
                                        class="bi bi-eye"></i></a>
                            </div>
                            <div class="product-body">
                                <span class="text-muted small"><?php echo e($p->generic->name ?? ''); ?></span>
                                <h6 class="mb-1"><a href="<?php echo e(route('shop.show', $p)); ?>"><?php echo e($p->name); ?></a></h6>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-bold text-primary">৳<?php echo e(number_format($p->sale_price, 2)); ?></span>
                                    <a href="<?php echo e(route('shop.show', $p)); ?>" class="btn btn-sm btn-outline-primary"><i
                                            class="bi bi-cart-plus"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="text-center text-muted">No featured products yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Why choose us / Accordion FAQ -->
    <section class="py-5">
        <div class="container">
            <div class="row g-5 align-items-center">
                <div class="col-lg-6" data-aos="fade-right">
                    <span class="badge bg-primary-subtle text-primary mb-2">FAQ</span>
                    <h2 class="mb-4">Frequently Asked Questions</h2>
                    <div class="accordion" id="faqAccordion">
                        <?php $__empty_1 = true; $__currentLoopData = $faqs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $faq): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button <?php echo e($loop->first ? '' : 'collapsed'); ?>"
                                        data-bs-toggle="collapse" data-bs-target="#faq<?php echo e($faq->id); ?>">
                                        <?php echo e($faq->question); ?>

                                    </button>
                                </h2>
                                <div id="faq<?php echo e($faq->id); ?>"
                                    class="accordion-collapse collapse <?php echo e($loop->first ? 'show' : ''); ?>"
                                    data-bs-parent="#faqAccordion">
                                    <div class="accordion-body"><?php echo e($faq->answer); ?></div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <p class="text-muted">No FAQs added yet.</p>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-lg-6" data-aos="fade-left">
                    <img src="<?php echo e(!empty($siteSettings['faq_image']) ? Storage::url($siteSettings['faq_image']) : 'https://images.unsplash.com/photo-1587854692152-cbe660dbde88?w=800'); ?>"
                        class="img-fluid rounded-4 shadow" alt="Pharmacy">
                </div>
            </div>
        </div>
    </section>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        new Swiper(".heroSwiper", {
            loop: true,
            autoplay: {
                delay: 4500
            },
            effect: "fade",
            pagination: {
                el: ".swiper-pagination",
                clickable: true
            },
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev"
            },
        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.frontend', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\pharmacy\resources\views/frontend/home/index.blade.php ENDPATH**/ ?>