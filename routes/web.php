<?php

use App\Http\Controllers\Admin\AuthorControllers;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PayPalController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\RentController;
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderControllers;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\BookControllers;
use App\Http\Controllers\Admin\RolePermissionController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\ReviewControllers;
use App\Http\Controllers\SubscriptionController;




Route::get('/about', function () {
    return view('about');
});

/* ================= Subscription paln ================= */
Route::get('/plans', [SubscriptionController::class, 'index'])
    ->name('plans.index');

Route::post('/subscription/checkout', 
    [SubscriptionController::class, 'checkout'])
    ->middleware('auth')
    ->name('subscription.checkout');

Route::get('/subscription/success', 
    [SubscriptionController::class, 'success'])
    ->middleware('auth')
    ->name('subscription.success');

    Route::post('/subscription/cancel', 
    [SubscriptionController::class, 'cancel'])
    ->middleware('auth')
    ->name('subscription.cancel');

    Route::post('/subscription/resume', 
    [SubscriptionController::class, 'resume'])
    ->middleware('auth')
    ->name('subscription.resume');







/* ================= HOME ================= */
Route::get('/', [BookController::class, 'home'])->name('home');
     Route::get('/products/{id}', [BookController::class, 'show'])
    ->name('product.details');

/*faq*/
Route::get('/faq', [FaqController::class, 'index'])->name('faq.index');


/* newsletter */
Route::get('/newsletter', [NewsletterController::class, 'index'])
    ->name('newsletter.index');

Route::post('/newsletter', [NewsletterController::class, 'subscribe'])
    ->name('newsletter.subscribe');


/* ================= BOOK DETAILS ================= */
Route::get('/books/{book}', [BookController::class, 'show'])->name('books.show');


/* ================= FORMAT PAGES ================= */
Route::get('/ebooks', [BookController::class, 'ebooks'])->name('ebooks');
Route::get('/audiobooks', [BookController::class, 'audiobooks'])->name('audiobooks');
Route::get('/paperbacks', [BookController::class, 'paperbacks'])->name('paperbacks');
Route::get('/ebooks/category/{category:slug}', [BookController::class, 'categoryBooks'])
     ->name('category.books');

Route::get('/categories', [BookController::class, 'categoriesIndex'])
    ->name('categories.index');



/*productcontroller*/

/* FORMAT-BASED PAGES */
Route::get('/ebooks', [ProductController::class, 'ebooks'])->name('ebooks.index');
Route::get('/audiobooks', [ProductController::class, 'audiobooks'])->name('audiobooks.index');
Route::get('/paperbacks', [ProductController::class, 'paperbacks'])->name('paperbacks.index');

/* BOOK DETAILS */
Route::get('/products/{id}', [ProductController::class, 'show'])
     ->name('product.details');
Route::get('/category/{category:slug}', [ProductController::class, 'categoryBooks'])
     ->name('category.books');

     Route::get('/products', [ProductController::class, 'home'])
     ->name('products.home');



/* ================= CART ================= */
Route::middleware('auth')->group(function () {
    Route::post('/cart/add/{book}', [CartController::class, 'add'])->name('cart.add');
    Route::get('/cart', [CartController::class, 'view'])->name('cart.view');
    Route::delete('/cart/item/{item}', [CartController::class, 'remove'])->name('cart.remove');
    Route::patch('/cart/item/{item}', [CartController::class, 'update'])
    ->name('cart.update');
    Route::post('/cart/coupon', [CartController::class, 'applyCoupon'])
    ->name('cart.coupon');
    Route::delete('/cart/coupon', [CartController::class, 'removeCoupon'])
    ->name('cart.coupon.remove');
});



/* ================= ORDERS ================= */
Route::middleware('auth')->group(function () {
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{order}/success', [OrderController::class, 'success'])->name('orders.success');
});
Route::middleware('auth')->group(function () {
    Route::get('/orders', [OrderController::class, 'index'])
        ->name('orders.index');
});
// Orders list
// Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');

// Show single order
Route::get('/orders/{order}', [OrderController::class, 'show'])
    ->middleware('auth')
    ->name('orders.show');

// Download invoice for an order
Route::get('/orders/{order}/invoice', [OrderController::class, 'downloadInvoice'])
    ->middleware('auth')
    ->name('orders.invoice');




/* Review book */

// Route::post('/book/{id}/review', [ReviewController::class, 'store'])
//     ->middleware('auth')
//     ->name('reviews.store');
Route::post('/books/{book}/reviews', [ReviewController::class, 'store'])
    ->middleware('auth')
    ->name('reviews.store');

Route::get('/reviews/{review}/edit', [ReviewController::class, 'edit'])
    ->middleware('auth')
    ->name('reviews.edit');

Route::put('/reviews/{review}', [ReviewController::class, 'update'])
    ->middleware('auth')
    ->name('reviews.update');

Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])
    ->middleware('auth')
    ->name('reviews.destroy');


// language switch

Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'hi', 'ta', 'te'])) {
        session(['locale' => $locale]);
    }
    return back();
})->name('lang.switch');







/* Stripe payment routes */
Route::get('/checkout/stripe/{order}', [StripeController::class, 'checkout'])
    ->name('stripe.checkout');

Route::get('/checkout/success/{order}', [StripeController::class, 'success'])
    ->name('stripe.success');

Route::get('/checkout', [StripeController::class, 'checkout'])->name('checkout');
Route::get('/payment-success', [StripeController::class, 'success'])->name('payment.success');
Route::get('/payment-cancel', [StripeController::class, 'cancel'])->name('payment.cancel');
Route::get('/stripe/checkout/{order}', 
    [StripeController::class, 'checkout']
)->name('stripe.checkout');


/* Paypal page route */
Route::get('/paypal/{order}/pay', [PayPalController::class, 'pay'])->name('paypal.pay');
Route::get('/paypal/{order}/success', [PayPalController::class, 'success'])
    ->name('paypal.success');
Route::get('/paypal/{order}/cancel', [PayPalController::class, 'cancel'])
    ->name('paypal.cancel');


// web.php

/* Payment control for multi pay gateway*/ 
Route::post('/payment/{order}/process', [PaymentController::class, 'process'])
    ->name('payment.gateway.process'); 

Route::get('/paypal/checkout/{order}', function ($orderId) {
    return "PayPal Checkout Page for Order #$orderId";
})->name('paypal.checkout');


// Checkout Routes
Route::middleware('auth')->group(function () {
    // Cart checkout flow
    Route::get('/checkout', [CheckoutController::class, 'index'])
        ->name('checkout.index');
    
    Route::post('/checkout/process', [CheckoutController::class, 'process'])
        ->name('checkout.process');

    
    // Payment routes
    Route::get('/checkout/payment/{order}', [CheckoutController::class, 'paymentPage'])
        ->name('payment.page');
    
    Route::post('/checkout/payment/{order}', [CheckoutController::class, 'processPayment'])
        ->name('payment.process');
    
    // Buy Now flow
    Route::post('/buy-now/{book}', [CheckoutController::class, 'buyNow'])
        ->name('buy.now');
    
    Route::get('/checkout/{order}/address', [CheckoutController::class, 'addressBuyNow'])
        ->name('checkout.address.buynow');
    
    Route::post('/checkout/{order}/address', [CheckoutController::class, 'storeBuyNowAddress'])
        ->name('checkout.address.store.buynow');
    
    // Order success
    Route::get('/orders/{order}/success', [CheckoutController::class, 'success'])
        ->name('orders.success');
});



/* AUTHORS */
Route::get('/authors', [AuthorController::class, 'index'])->name('authors.index');
Route::get('/authors/{id}', [AuthorController::class, 'show'])->name('authors.show');



/*Rent control */
Route::post('/ebook/rent/{book}', [RentController::class, 'rentEbook'])
    ->name('ebook.rent');
Route::post('/audio/rent/{book}', [RentController::class, 'rentAudio'])
    ->name('audio.rent');


 // Wishlist Routes
Route::middleware('auth')->group(function () {
    Route::get('/wishlist', [WishlistController::class, 'index'])
        ->name('wishlist.index');
        
    Route::post('/wishlist/toggle', [WishlistController::class, 'toggle'])
        ->name('wishlist.toggle');
    //     Route::post('/wishlist/{book}', [WishlistController::class, 'store'])
    //         ->name('wishlist.store');
        
    Route::delete('/wishlist/{wishlist}', [WishlistController::class, 'destroy'])
        ->name('wishlist.destroy');
});

/* Library route */
Route::post('/library/add/{book}', [LibraryController::class, 'add'])
    ->middleware('auth');
 Route::get('/my-library', [LibraryController::class, 'index'])
     ->middleware('auth')
    ->name('library.index');


/* Admin Dashboard start here */



Route::middleware(['auth','admin'])->group(function () {
    Route::get('/admin/notifications', function () {
        return view('admin.notifications.index', [
            'notifications' => Auth::user()->notifications
        ]);
    })->name('admin.notifications.index');

Route::get('/notifications/read/{id}', function ($id) {
    $notification = Auth::user()->notifications
        ->where('id', $id)
        ->firstOrFail();

    $notification->markAsRead();

    return redirect($notification->data['url'] ?? '/');
})->name('admin.notifications.read');

});





Route::middleware(['auth','admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::resource('reviews', \App\Http\Controllers\Admin\ReviewControllers::class)
            ->only(['index','destroy']);

        Route::patch('reviews/{review}/approve',
            [\App\Http\Controllers\Admin\ReviewControllers::class,'approve'])
            ->name('reviews.approve');
});


Route::middleware(['auth','role:admin,manager'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

    Route::resource('books', BookControllers::class);
    Route::resource('authors', AuthorControllers::class);
});


Route::middleware(['auth', 'role:admin,staff'])->prefix('admin')->group(function () {

    Route::get('/users', [UserController::class, 'index'])
        ->middleware('permission:users.view')
        ->name('admin.users.index');

    Route::get('/users/create', [UserController::class, 'create'])
        ->middleware('permission:users.create')
        ->name('admin.users.create');
    Route::post('/users', [UserController::class, 'store'])
        ->middleware('permission:users.create')
        ->name('admin.users.store');

    Route::get('/users/{user}/edit', [UserController::class, 'edit'])
        ->middleware('permission:users.edit')
        ->name('admin.users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])
        ->middleware('permission:users.edit')
        ->name('admin.users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])
        ->middleware('permission:users.delete')
        ->name('admin.users.destroy');


});

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/roles-permissions', [RolePermissionController::class, 'index'])
            ->middleware('permission:manage_roles_permissions')
            ->name('roles_permissions.index');
        Route::put('/roles-permissions', [RolePermissionController::class, 'update'])
            ->middleware('permission:manage_roles_permissions')
            ->name('roles_permissions.update');
    });

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::put('/orders/{id}/status', 
        [OrderControllers::class, 'updateStatus']
    )->name('admin.orders.updateStatus');

    Route::put('/orders/{id}/payment-status', 
        [OrderControllers::class, 'updatePaymentStatus']
    )->name('admin.orders.updatePaymentStatus');

});


Route::get('/orders/export/csv', [OrderControllers::class, 'exportCsv'])
    ->name('admin.orders.export');


Route::middleware(['auth', 'role:admin,manager,staff'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
    Route::get('/orders', [OrderControllers::class, 'index'])->name('orders.index');
    Route::put('/orders/{order}', [OrderControllers::class, 'update'])->name('orders.update');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {

    Route::get('/orders/{id}', [OrderController::class, 'show'])
        ->name('admin.orders.show');

});

Route::middleware(['auth', 'role:admin,manager,staff,user'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('admin.dashboard');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/payments', [OrderControllers::class, 'payments'])
    ->name('admin.payments.index');
});


Route::get('/profile', [ProfileController::class, 'index'])
    ->middleware('auth')
    ->name('profile');

    Route::put('/profile/update', 
    [ProfileController::class, 'update'])
    ->middleware('auth')
    ->name('profile.update');

    Route::post('/profile/cover', 
    [ProfileController::class, 'updateCover'])
    ->middleware('auth')
    ->name('profile.cover');


    Route::post('/profile/avatar', 
    [ProfileController::class, 'updateAvatar'])
    ->middleware('auth')
    ->name('profile.avatar');


require __DIR__.'/auth.php';

