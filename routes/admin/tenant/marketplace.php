<?php
/*
 *  MARKETPLACE ROUTES
 */
Route::get('/marketplace', 'Admin\MarketplaceController@index')->name('admin.marketplace.index');
Route::get('/marketplace/taxon/{code}', 'Admin\MarketplaceController@products')->name('admin.marketplace.products');
Route::get('/marketplace/product/{code}', 'Admin\MarketplaceController@product')->name('admin.marketplace.product');
Route::get('/marketplace/cart', 'Admin\MarketplaceController@cart')->name('admin.marketplace.cart');
Route::post('/marketplace/add-to-cart', 'Admin\MarketplaceController@addToCart')->name('admin.marketplace.add_to_cart');
Route::post('/marketplace/update-cart', 'Admin\MarketplaceController@updateCart')->name('admin.marketplace.update_cart');
Route::post('/marketplace/remove-from-cart', 'Admin\MarketplaceController@removeFromCart')->name('admin.marketplace.remove_from_cart');
Route::get('/marketplace/remove-cart', 'Admin\MarketplaceController@removeCart')->name('admin.marketplace.remove_cart');
//Route::get('/marketplace/complete-order', 'Admin\MarketplaceController@completeOrder')->name('admin.marketplace.complete_order');
Route::get('/marketplace/address', 'Admin\MarketplaceController@address')->name('admin.marketplace.address');
Route::get('/marketplace/new-address', 'Admin\MarketplaceController@newAddress')->name('admin.marketplace.new-address');
Route::post('/marketplace/new-address', 'Admin\MarketplaceController@addAddres')->name('admin.marketplace.addAddress');
Route::post('/marketplace/addAddress', 'Admin\MarketplaceController@addAddress')->name('admin.marketplace.addAddress');
Route::post('/marketplace/deleteAddress', 'Admin\MarketplaceController@deleteAddress')->name('admin.marketplace.deleteAddress');
Route::get('/marketplace/edit-address/{id}', 'Admin\MarketplaceController@editAddress')->name('admin.marketplace.editAddress');
Route::post('/marketplace/updateAddress/{id}', 'Admin\MarketplaceController@updateAddress')->name('admin.marketplace.updateAddress');
Route::get('/marketplace/checkout', 'Admin\MarketplaceController@checkout')->name('admin.marketplace.checkout');
//Route::post('/marketplace/checkout', 'Admin\MarketplaceController@checkout')->name('admin.marketplace.checkout');


Route::get('/marketplace/orders-history', 'Admin\MarketplaceController@orderHistory')->name('admin.marketplace.orders_history');
Route::get('/marketplace/history/{orderId}', 'Admin\MarketplaceController@getOrderHistoryDetails')->name('admin.marketplace.order_history_order_details');

