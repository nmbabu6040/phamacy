<?php

use App\Http\Controllers\Admin\{
    ActivityLogController,
    AboutFeatureController,
    AboutValueController,
    BackupController,
    BarcodeController,
    BatchController,
    BranchController,
    BrandController,
    CategoryController,
    CounterController,
    CourierController,
    CustomerController,
    DashboardController,
    ExpenseCategoryController,
    ExpenseController,
    FaqController,
    GenericController,
    OrderController,
    ProductController,
    PurchaseController,
    ReportController,
    RoleController,
    SaleController,
    SettingController,
    SliderController,
    SupplierController,
    UnitController,
    UserController,
    ContactMessageController,
    NewsletterSubscriberController
};
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Frontend\CartController;
use App\Http\Controllers\Frontend\CheckoutController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\OrderTrackController;
use App\Http\Controllers\Frontend\ShopController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Frontend routes
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::post('/contact', [HomeController::class, 'contactSubmit'])->name('contact.submit');
Route::post('/newsletter/subscribe', [HomeController::class, 'newsletterSubscribe'])
    ->name('newsletter.subscribe');
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/shop/{product:slug}', [ShopController::class, 'show'])->name('shop.show');

Route::get('/lang/{locale}', function (string $locale) {
    session(['locale' => in_array($locale, ['en', 'bn']) ? $locale : 'en']);
    return back();
})->name('lang.switch');

/*
|--------------------------------------------------------------------------
| Online checkout (guest cart via session, no customer login required)
|--------------------------------------------------------------------------
*/
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
Route::patch('/cart/update/{product}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/remove/{product}', [CartController::class, 'remove'])->name('cart.remove');

Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/order/success/{sale}', [CheckoutController::class, 'success'])->name('checkout.success');

Route::get('/track-order', [OrderTrackController::class, 'index'])->name('order.track');
Route::post('/track-order', [OrderTrackController::class, 'find'])->name('order.track.find');

/*
|--------------------------------------------------------------------------
| Auth routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

/*
|--------------------------------------------------------------------------
| Admin / backend routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware(['auth', 'log.activity', 'current.branch'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    /*
    |----------------------------------------------------------------------
    | Catalog / inventory
    |----------------------------------------------------------------------
    */
    Route::resource('products', ProductController::class)->except(['show'])
        ->middleware('permission:product.view|product.create|product.edit|product.delete');
    Route::post('products/{product}/stock', [ProductController::class, 'adjustStock'])->name('products.stock');

    Route::resource('categories', CategoryController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('generics', GenericController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('units', UnitController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('brands', BrandController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('suppliers', SupplierController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('customers', CustomerController::class)->only(['index', 'store', 'update', 'destroy']);

    /*
    |----------------------------------------------------------------------
    | Purchase (stock IN)
    |----------------------------------------------------------------------
    */
    Route::resource('purchases', PurchaseController::class)->only(['index', 'create', 'store', 'show', 'destroy'])
        ->middleware('permission:purchase.view|purchase.create|purchase.delete');
    Route::get('purchases/{purchase}/pdf', [PurchaseController::class, 'pdf'])->name('purchases.pdf');

    /*
    |----------------------------------------------------------------------
    | Sales / POS (stock OUT)
    |----------------------------------------------------------------------
    */
    Route::get('pos', [SaleController::class, 'pos'])->name('sales.pos');
    Route::get('pos/search', [SaleController::class, 'posSearch'])->name('sales.pos.search');
    Route::resource('sales', SaleController::class)->only(['index', 'store', 'show', 'destroy'])
        ->middleware('permission:sale.view|sale.create|sale.delete');
    Route::get('sales/{sale}/pdf', [SaleController::class, 'pdf'])->name('sales.pdf');

    /*
    |----------------------------------------------------------------------
    | Online orders (same Sale model, channel = online)
    |----------------------------------------------------------------------
    */
    Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{sale}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('orders/{sale}/status', [OrderController::class, 'updateStatus'])->name('orders.status');

    /*
    |----------------------------------------------------------------------
    | Batches (expiry / FEFO stock)
    |----------------------------------------------------------------------
    */
    Route::get('batches', [BatchController::class, 'index'])->name('batches.index');
    Route::get('batches/expiring', [BatchController::class, 'expiring'])->name('batches.expiring');

    /*
    |----------------------------------------------------------------------
    | Barcode / QR label printing
    |----------------------------------------------------------------------
    */
    Route::get('barcodes', [BarcodeController::class, 'index'])->name('barcodes.index');
    Route::get('barcodes/print', [BarcodeController::class, 'print'])->name('barcodes.print');

    /*
    |----------------------------------------------------------------------
    | Branches
    |----------------------------------------------------------------------
    */
    Route::resource('branches', BranchController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::post('branches/switch', [BranchController::class, 'switch'])->name('branches.switch');

    /*
    |----------------------------------------------------------------------
    | Courier booking (single + bulk)
    |----------------------------------------------------------------------
    */
    Route::prefix('courier')->name('courier.')->group(function () {
        Route::get('/', [CourierController::class, 'index'])->name('index');
        Route::post('book/{sale}', [CourierController::class, 'bookOrder'])->name('book');
        Route::post('book-adhoc', [CourierController::class, 'bookAdhoc'])->name('book-adhoc');
        Route::get('bulk', [CourierController::class, 'bulkForm'])->name('bulk.form');
        Route::post('bulk', [CourierController::class, 'bulkUpload'])->name('bulk.upload');
        Route::get('template', [CourierController::class, 'downloadTemplate'])->name('template');
        Route::post('{booking}/refresh', [CourierController::class, 'refreshStatus'])->name('refresh');
    });

    /*
    |----------------------------------------------------------------------
    | Expenses
    |----------------------------------------------------------------------
    */
    Route::resource('expense-categories', ExpenseCategoryController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('expenses', ExpenseController::class)->only(['index', 'store', 'destroy']);

    /*
    |----------------------------------------------------------------------
    | Reports
    |----------------------------------------------------------------------
    */
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('sales', [ReportController::class, 'sales'])->name('sales');
        Route::get('purchases', [ReportController::class, 'purchases'])->name('purchases');
        Route::get('expenses', [ReportController::class, 'expenses'])->name('expenses');
        Route::get('profit-loss', [ReportController::class, 'profitLoss'])->name('profit-loss');
        Route::get('inventory', [ReportController::class, 'inventory'])->name('inventory');
    });

    /*
    |----------------------------------------------------------------------
    | Website content management (Sliders, Counters, FAQs, About page)
    |----------------------------------------------------------------------
    */
    Route::resource('sliders', SliderController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('counters', CounterController::class)->except(['show'])->names('counters');
    Route::resource('faqs', FaqController::class)->except(['show'])->names('faqs');
    Route::resource('about-features', AboutFeatureController::class)->except(['show'])->names('about-features');
    Route::resource('about-values', AboutValueController::class)->except(['show'])->names('about-values');


    /*
|--------------------------------------------------------------------------
| Contact Messages & Newsletter Subscribers
|--------------------------------------------------------------------------
*/

    Route::get('contact-messages', [ContactMessageController::class, 'index'])
        ->name('contact-messages.index');

    Route::get('contact-messages/{contactMessage}', [ContactMessageController::class, 'show'])
        ->name('contact-messages.show');

    Route::put('contact-messages/{contactMessage}', [ContactMessageController::class, 'update'])
        ->name('contact-messages.update');

    Route::delete('contact-messages/{contactMessage}', [ContactMessageController::class, 'destroy'])
        ->name('contact-messages.destroy');


    Route::get('newsletter-subscribers', [NewsletterSubscriberController::class, 'index'])
        ->name('newsletter-subscribers.index');

    Route::put('newsletter-subscribers/{newsletterSubscriber}', [NewsletterSubscriberController::class, 'update'])
        ->name('newsletter-subscribers.update');

    Route::delete('newsletter-subscribers/{newsletterSubscriber}', [NewsletterSubscriberController::class, 'destroy'])
        ->name('newsletter-subscribers.destroy');

    /*
    |----------------------------------------------------------------------
    | Role & permission / users / settings / logs / backup — admin only
    |----------------------------------------------------------------------
    */
    Route::middleware('role:admin')->group(function () {
        Route::resource('roles', RoleController::class)->only(['index', 'store', 'destroy']);
        Route::get('roles/{role}/permissions', [RoleController::class, 'permissions'])->name('roles.permissions');
        Route::post('roles/{role}/permissions', [RoleController::class, 'updatePermissions'])->name('roles.permissions.update');
        Route::resource('users', UserController::class)->only(['index', 'store', 'update', 'destroy']);

        // Settings + SEO
        Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('settings', [SettingController::class, 'update'])->name('settings.update');

        // Activity log
        Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
        Route::delete('activity-logs/{activityLog}', [ActivityLogController::class, 'destroy'])->name('activity-logs.destroy');
        Route::delete('activity-logs', [ActivityLogController::class, 'clear'])->name('activity-logs.clear');

        // Backup
        Route::get('backups', [BackupController::class, 'index'])->name('backups.index');
        Route::post('backups/run', [BackupController::class, 'run'])->name('backups.run');
        Route::get('backups/{file}/download', [BackupController::class, 'download'])->name('backups.download');
        Route::delete('backups/{file}', [BackupController::class, 'destroy'])->name('backups.destroy');
    });
});
