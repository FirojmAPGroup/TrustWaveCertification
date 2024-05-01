<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::group(['prefix' => '', 'namespace' => '\App\Http\Controllers', 'middleware' => []], function () {
    Route::group(['prefix' => '', 'middleware' => ['web']], function () {
        // Without Login
        Route::group(['prefix' => ''], function () {
            Route::get('/login', 'AuthController@login')->name('app.login');
            Route::post('/login','AuthController@loginSubmit')->name('app.loginsubmit');
            Route::get('/logout','AuthController@logout')->name('app.logout');
            Route::get('/forgot-password', 'AuthController@forgotPassword')->name('password.request');
            Route::post('/forgot-password', 'AuthController@forgotPasswordSubmit')->name('password.email');
            Route::get('/reset-password/{token}','AuthController@passwordReset')->name('password.reset');
            Route::post('/reset-password','AuthController@passwordResetSubmit')->name('password.update');

       });
    });
    Route::group(['middleware'=>['web','user']],function (){
        Route::get('', 'DashboardController@dashboard')->name('app.dashboard');
        //Sub Admin
        Route::get('sub-admin', 'SubAdminController@index')->name('subadmin.list');
        Route::post('sub-admin', 'SubAdminController@loadList')->name('subadmin.load-list');
        Route::get('sub-admin/create', 'SubAdminController@create')->name('subadmin.create');
        Route::post('sub-admin/save', 'SubAdminController@save')->name('subadmin.save');
        Route::get('sub-admin/edit/{id}', 'SubAdminController@edit')->name('subadmin.edit');
        Route::post('sub-admin/delete/{id}', 'SubAdminController@delete')->name('subadmin.delete');
        Route::post('sub-admin/approve/{id}', 'SubAdminController@statusChange')->name('subadmin.aprove');
        Route::post('sub-admin/reject/{id}', 'SubAdminController@statusChange')->name('subadmin.reject');
        Route::post('sub-admin/block/{id}', 'SubAdminController@statusChange')->name('subadmin.block');
        //Teams

        Route::get('teams', 'TeamsController@index')->name('teams.list');
        Route::post('teams', 'TeamsController@loadList')->name('teams.loadList');
        Route::get('teams/create', 'TeamsController@create')->name('teams.create');
        Route::post('teams/save', 'TeamsController@save')->name('teams.save');
        Route::get('teams/edit/{id}', 'TeamsController@edit')->name('teams.edit');
        Route::post('teams/delete/{id}', 'TeamsController@delete')->name('teams.delete');
        Route::post('teams/approve/{id}', 'TeamsController@statusChange')->name('teams.aprove');
        Route::post('teams/reject/{id}', 'TeamsController@statusChange')->name('teams.reject');
        Route::post('teams/block/{id}', 'TeamsController@statusChange')->name('teams.block');
        //pages
        Route::match(['get','post'],'/pages/about-us', 'PagesController@aboutUsForm')->name('pages.about-us');
        Route::match(['get','post'],'/pages/privacy-policy', 'PagesController@privacyPolicyForm')->name('pages.privacy-policy');
        Route::match(['get','post'],'/pages/terms-condition', 'PagesController@termsConditionForm')->name('pages.terms-condition');
        Route::match(['get','post'],'/pages/contact-us', 'PagesController@contactUsForm')->name('pages.contact-us');


        //Leads
        Route::get('/leads/create','LeadsController@create')->name('leads.create');
        Route::get('/leads/edit/{id}','LeadsController@create')->name('leads.edit');
        Route::post('/leads/save','LeadsController@save')->name('leads.save');
        Route::get('/leads','LeadsController@index')->name('leads.list');
        Route::post('/leads','LeadsController@loadList')->name('leads.loadlist');
        //Asign Lead
        Route::get('/leads/asign','LeadsController@asignLead')->name('leads.asign');
        Route::post('/leads/asign','LeadsController@leadAsign')->name('leads.submitAsign');
    });
});
