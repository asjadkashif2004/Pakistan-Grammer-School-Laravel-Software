<?php

use App\Http\Controllers\AlertController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\FeeManagementController;
use App\Http\Controllers\FeeDefaulterListController;
use App\Http\Controllers\FeeVoucherController;
use App\Http\Controllers\FeeVoucherListController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\SalesInvoiceController;
use App\Http\Controllers\StaffSalaryController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/alerts', [AlertController::class, 'index'])->name('alerts.index');
    Route::get('/alerts/unread-count', [AlertController::class, 'unreadCount'])->name('alerts.unread-count');
    Route::post('/alerts/mark-read', [AlertController::class, 'markAllRead'])->name('alerts.mark-read');
    Route::get('/students/registration', [StudentController::class, 'index'])->name('students.index');
    Route::post('/students/registration', [StudentController::class, 'store'])->name('students.store');
    Route::get('/students/sibling-discount', [StudentController::class, 'siblingDiscountPreview'])->name('students.sibling-discount');
    Route::get('/get-fee/{classId}', [FeeManagementController::class, 'getFee'])->name('students.get-fee');
    Route::get('/students/roster', [StudentController::class, 'roster'])->name('students.roster');
    Route::get('/students/roster/print', [StudentController::class, 'rosterPrint'])->name('students.roster.print');
    Route::get('/students/roster/{student}', [StudentController::class, 'show'])->name('students.show');
    Route::get('/students/{student}/print', [StudentController::class, 'print'])->name('students.print');
    Route::get('/students/{student}/edit', [StudentController::class, 'edit'])->name('students.edit');
    Route::put('/students/{student}', [StudentController::class, 'update'])->name('students.update');
    Route::delete('/students/{student}', [StudentController::class, 'destroy'])->name('students.destroy');

    Route::get('/fee-vouchers', [FeeVoucherController::class, 'index'])->name('fee-vouchers.index');
    Route::get('/fee-vouchers/lists/pending', [FeeVoucherListController::class, 'pending'])->name('fee-vouchers.list.pending');
    Route::redirect('/fee-vouchers/lists/overdue', '/fee-vouchers/lists/pending');
    Route::get('/fee-vouchers/lists/paid', [FeeVoucherListController::class, 'paid'])->name('fee-vouchers.list.paid');
    Route::get('/fee-vouchers/lists/defaulters', [FeeDefaulterListController::class, 'index'])->name('fee-vouchers.list.defaulters');
    Route::post('/fee-vouchers', [FeeVoucherController::class, 'store'])->name('fee-vouchers.store');
    Route::post('/fee-vouchers/{feeCollection}/payments', [FeeVoucherController::class, 'storePayment'])->name('fee-vouchers.payments.store');
    Route::delete('/fee-vouchers/{feeCollection}', [FeeVoucherController::class, 'destroy'])->name('fee-vouchers.destroy');
    Route::get('/fee-vouchers/{feeCollection}/edit', [FeeVoucherController::class, 'edit'])->name('fee-vouchers.edit');
    Route::put('/fee-vouchers/{feeCollection}', [FeeVoucherController::class, 'update'])->name('fee-vouchers.update');
    Route::get('/fee-vouchers/{feeCollection}/print', [FeeVoucherController::class, 'print'])->name('fee-vouchers.print');
    Route::get('/fee-vouchers/{feeCollection}/download', [FeeVoucherController::class, 'download'])->name('fee-vouchers.download');

    Route::get('/fee-management', [FeeManagementController::class, 'index'])->name('fee-management.index');
    Route::post('/fee-management', [FeeManagementController::class, 'store'])->name('fee-management.store');
    Route::put('/fee-management/{classFee}', [FeeManagementController::class, 'update'])->name('fee-management.update');
    Route::delete('/fee-management/{classFee}', [FeeManagementController::class, 'destroy'])->name('fee-management.destroy');

    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
    Route::get('/products-print', [ProductController::class, 'print'])->name('products.print');

    Route::get('/invoices-sales', [SalesInvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/invoices-sales/student-lookup', [SalesInvoiceController::class, 'studentLookup'])->name('invoices.student-lookup');
    Route::get('/invoices-sales/product-lookup', [SalesInvoiceController::class, 'productLookup'])->name('invoices.product-lookup');
    Route::post('/invoices-sales', [SalesInvoiceController::class, 'store'])->name('invoices.store');
    Route::get('/invoices-sales/{invoice}/print', [SalesInvoiceController::class, 'print'])->name('invoices.print');
    Route::get('/invoices-sales/{invoice}/download', [SalesInvoiceController::class, 'download'])->name('invoices.download');

    Route::get('/staff-salaries', [StaffSalaryController::class, 'index'])->name('staff-salaries.index');
    Route::post('/staff-salaries/employees', [StaffSalaryController::class, 'storeEmployee'])->name('staff-salaries.employees.store');
    Route::get('/staff-salaries/employees/{staffMember}/edit', [StaffSalaryController::class, 'editEmployee'])->name('staff-salaries.employees.edit');
    Route::put('/staff-salaries/employees/{staffMember}', [StaffSalaryController::class, 'updateEmployee'])->name('staff-salaries.employees.update');
    Route::delete('/staff-salaries/employees/{staffMember}', [StaffSalaryController::class, 'destroyEmployee'])->name('staff-salaries.employees.destroy');
    Route::post('/staff-salaries/{staffMember}/advance', [StaffSalaryController::class, 'addAdvance'])->name('staff-salaries.advance');
    Route::post('/staff-salaries/{staffMember}/overtime', [StaffSalaryController::class, 'addExtraHours'])->name('staff-salaries.overtime');
    Route::post('/staff-salaries/{staffMember}/pay-wage', [StaffSalaryController::class, 'payWage'])->name('staff-salaries.pay-wage');
    Route::post('/staff-salaries/{staffMember}/undo-payment', [StaffSalaryController::class, 'undoPayment'])->name('staff-salaries.undo-payment');

    Route::get('/expenses', [ExpenseController::class, 'index'])->name('expenses.index');
    Route::post('/expenses', [ExpenseController::class, 'store'])->name('expenses.store');
    Route::get('/expenses/{expense}/edit', [ExpenseController::class, 'edit'])->name('expenses.edit');
    Route::put('/expenses/{expense}', [ExpenseController::class, 'update'])->name('expenses.update');
    Route::delete('/expenses/{expense}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');

    Route::get('/reports', [ReportsController::class, 'index'])->name('reports.index');
    Route::get('/reports/print/{report}', [ReportsController::class, 'print'])->name('reports.print');

});

require __DIR__.'/auth.php';
