<?php


use App\Http\Controllers\Agent\AgentServiceSellingController;
use App\Http\Controllers\Frontend\FrontendServiceController;
use App\Http\Controllers\Admin\AdminServiceSellingController;
use App\Http\Controllers\Customer\CustomerServiceSellingController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;



Route::prefix('admin')->middleware(['auth', 'anyAuth'])->group(function () {
  Route::get('service/create/{type}/{listing_id}', [AdminServiceSellingController::class, 'service_create'])->name('admin.service.create'); 
  Route::post('services/stores', [AdminServiceSellingController::class, 'service_store'])->name('admin.service.stores'); 
  Route::get('service/edit/{type}/{listing_id}/{id}', [AdminServiceSellingController::class, 'service_edit'])->name('admin.service.edit'); 
  Route::post('service/update/{id}', [AdminServiceSellingController::class, 'service_update'])->name('admin.service.update'); 
  Route::get('service/delete/{id}', [AdminServiceSellingController::class, 'service_delete'])->name('admin.service.delete'); 

  Route::get('all-services', [AdminServiceSellingController::class, 'all_services'])->name('admin.all_services');
  Route::get('all-services/delete/{id}', [AdminServiceSellingController::class, 'all_services_delete'])->name('admin.all_services.delete');
  Route::post('all-services/status/{id}', [AdminServiceSellingController::class, 'all_services_status'])->name('admin.all_services.status'); 

   Route::get('service/myservice', [AdminServiceSellingController::class, 'myservice'])->name('admin.service.myservice'); 
   Route::get('service_manager', [AdminServiceSellingController::class, 'service_manager'])->name('admin.service.manager'); 
   Route::get('service_manager/calendar', [AdminServiceSellingController::class, 'service_manager_calendar'])->name('admin.service.manager.calendar'); 
   Route::get('service_manager/stats', [AdminServiceSellingController::class, 'service_stats'])->name('admin.service.manager.stats'); 
   Route::get('service_manager/export/calendar/{id}', [AdminServiceSellingController::class, 'export_calendar'])->name('admin.service.export.calendar'); 
   Route::get('service_manager/export/all-calendars', [AdminServiceSellingController::class, 'export_all_calendars'])->name('admin.service.export.all_calendars'); 
   Route::get('service/manager/{id}', [AdminServiceSellingController::class, 'service_manager_delete'])->name('admin.service_manager.delete');
  Route::get('service_manager/paid/{id}', [AdminServiceSellingController::class, 'service_manager_paid'])->name('admin.service_manager.paid'); 
  Route::get('service_manager/unpaid/{id}', [AdminServiceSellingController::class, 'service_manager_Unpaid'])->name('admin.service_manager.unpaid'); 
  Route::get('service_manager/approve/{id}', [AdminServiceSellingController::class, 'service_manager_approve'])->name('admin.service_manager.approve'); 

   Route::get('employee/list', [AdminServiceSellingController::class, 'employee_list'])->name('admin.employee.list'); 
   Route::get('add_employee_form', [AdminServiceSellingController::class, 'add_employee_form'])->name('admin.add_employee_form'); 
   Route::post('employee/store', [AdminServiceSellingController::class, 'employee_store'])->name('admin.employee.store'); 
   Route::get('employee/edit/{id}', [AdminServiceSellingController::class, 'employee_edit'])->name('admin.employee.edit'); 
   Route::post('employee/update/{id}', [AdminServiceSellingController::class, 'employee_update'])->name('admin.employee.update'); 
   Route::get('employee/delete/{id}', [AdminServiceSellingController::class, 'employee_delete'])->name('admin.employee.delete'); 

});


Route::prefix('agent')->middleware(['auth', 'anyAuth'])->group(function () {
    Route::get('services/create/{type}/{listing_id}', [AgentServiceSellingController::class, 'service_create'])->name('agent.service.create'); 
    Route::post('services/store', [AgentServiceSellingController::class, 'service_store'])->name('agent.service.store'); 
    Route::get('services/edit/{type}/{listing_id}/{id}', [AgentServiceSellingController::class, 'service_edit'])->name('agent.service.edit'); 
    Route::post('service/update/{id}', [AgentServiceSellingController::class, 'service_update'])->name('agent.service.update'); 
    Route::get('service/delete/{id}', [AgentServiceSellingController::class, 'service_delete'])->name('agent.service.delete'); 
  
    Route::get('my-created-services', [AgentServiceSellingController::class, 'my_created_services'])->name('agent.my_created_services');
    Route::get('my-created-services/delete/{id}', [AgentServiceSellingController::class, 'my_created_services_delete'])->name('agent.my_created_services.delete');
    Route::post('my-created-services/status/{id}', [AgentServiceSellingController::class, 'my_created_services_status'])->name('agent.my_created_services.status');
  
     Route::get('services/myservice', [AgentServiceSellingController::class, 'myservice'])->name('agent.myservice');
     Route::get('service_managers', [AgentServiceSellingController::class, 'service_manager'])->name('agent.service.manager'); 
     Route::get('services/managers/{id}', [AgentServiceSellingController::class, 'service_manager_delete'])->name('agent.service_manager.delete'); 
     Route::get('service_managers/paid/{id}', [AgentServiceSellingController::class, 'service_manager_paid'])->name('agent.service_manager.paid'); 
     Route::get('service_managers/unpaid/{id}', [AgentServiceSellingController::class, 'service_manager_Unpaid'])->name('agent.service_manager.unpaid'); 
     Route::get('service_managers/approve/{id}', [AgentServiceSellingController::class, 'service_manager_approve'])->name('agent.service_manager.approve'); 

     Route::get('employee/list', [AgentServiceSellingController::class, 'employee_list'])->name('agent.employee.list'); 
    Route::get('add_employee_form', [AgentServiceSellingController::class, 'add_employee_form'])->name('agent.add_employee_form'); 
    Route::post('employee/store', [AgentServiceSellingController::class, 'employee_store'])->name('agent.employee.store'); 
    Route::get('employee/edit/{id}', [AgentServiceSellingController::class, 'employee_edit'])->name('agent.employee.edit'); 
    Route::post('employee/update/{id}', [AgentServiceSellingController::class, 'employee_update'])->name('agent.employee.update'); 
    Route::get('employee/delete/{id}', [AgentServiceSellingController::class, 'employee_delete'])->name('agent.employee.delete'); 

});


Route::controller(CustomerServiceSellingController::class)->middleware('auth', 'customer')->group(function () {
  Route::get('customer/service/myservice', 'myservice')->name('customer.myservice');
  Route::get('customer/service/manager/{id}', 'service_manager_delete')->name('customer.service_manager.delete');
  Route::post('customer/service/cancel/{id}', 'cancel_booking')->name('customer.service.cancel');
  Route::get('customer/service/favorites', 'favorite_providers')->name('customer.favorites');
  Route::get('customer/service/remove-favorite/{id}', 'remove_favorite')->name('customer.remove_favorite');

});


Route::get('/service/slot/{type}/{listing_id}/{id}', [FrontendServiceController::class, 'service_slot'])->name('service.slot');
Route::get('/service/booking_form/{type}/{id}/{day}/{date}/{slot_time}/{listing_id}/{employeeid}', [FrontendServiceController::class, 'service_booking_form'])->name('service.booking.form');
Route::post('/service/booking_store', [FrontendServiceController::class, 'service_booking_store'])->name('service.booking.store');

Route::get('/service/{id}/{listing_id}/slots', [FrontendServiceController::class, 'fetchSlots'])->name('service.fetchSlots');

Route::post('/service/add-to-cart', [FrontendServiceController::class, 'add_to_cart'])->name('service.add_to_cart');
Route::get('/service/cart', [FrontendServiceController::class, 'view_cart'])->name('service.cart');
Route::get('/service/remove-from-cart/{id}', [FrontendServiceController::class, 'remove_from_cart'])->name('service.remove_from_cart');
Route::post('/service/checkout', [FrontendServiceController::class, 'checkout'])->name('service.checkout');

Route::post('/service/favorite/toggle', [FrontendServiceController::class, 'toggle_favorite_employee'])->name('service.favorite.toggle');
Route::get('/service/favorites', [FrontendServiceController::class, 'get_favorite_employees'])->name('service.favorites');
Route::get('/service/similar/{serviceId}/{listingId}', [FrontendServiceController::class, 'get_similar_services'])->name('service.similar');

