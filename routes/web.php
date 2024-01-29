<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomAuthController;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;
use App\Http\Controllers\GmailController;
use App\Http\Controllers\BirthdayController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/clear-cache', function() {
    Artisan::call('cache:clear');
    return "Cache is cleared";
});

Auth::routes();

Route::get('/home', 'App\Http\Controllers\HomeController@index');
Route::get('/', 'App\Http\Controllers\HomeController@index')->name('home');
// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::any('users_list', array(
    'uses' => 'App\Http\Controllers\UserController@user',
    'as'   => 'users_list'
));

Route::any('add_user', array(
    'uses' => 'App\Http\Controllers\UserController@addEmployees',
    'as'   => 'add_user'
));

Route::any('employee_json', array(
    'uses' => 'App\Http\Controllers\UserController@employeejson',
    'as'   => 'employee_json'
));

Route::any('get_users/{user_id}', array(
    'uses' => 'App\Http\Controllers\UserController@getUsers',
    'as'   => 'get_users'
));

Route::any('update_users', array(
    'uses' => 'App\Http\Controllers\UserController@updateUser',
    'as'   => 'update_users'
));

Route::any('delete_user/{id}/{status}', array(
    'uses' => 'App\Http\Controllers\UserController@deleteUser',
    'as'   => 'delete_user'
));

Route::any('user_assigned', array(
    'uses' => 'App\Http\Controllers\UserController@userAssigned',
    'as'   => 'user_assigned'
));

Route::any('add_user_assigned', array(
    'uses' => 'App\Http\Controllers\UserController@addUserAssigned',
    'as'   => 'add_user_assigned'
));

Route::any('assigned_user_json', array(
    'uses' => 'App\Http\Controllers\UserController@assignedUserJson',
    'as'   => 'assigned_user_json'
));

Route::any('support_view', array(
    'uses' => 'App\Http\Controllers\SupportController@supportView',
    'as'   => 'support_view'
));
Route::any('support_json', array(
    'uses' => 'App\Http\Controllers\SupportController@supportJson',
    'as'   => 'support_json'
));
Route::any('update_status', array(
    'uses' => 'App\Http\Controllers\SupportController@updateStatus',
    'as'   => 'update_status'
));

Route::any('view_email_body/{id}', array(
    'uses' => 'App\Http\Controllers\SupportController@viewEmailBody',
    'as'   => 'view_email_body'
));
Route::any('update_support_status', array(
    'uses' => 'App\Http\Controllers\SupportController@updateSupportStatus',
    'as'   => 'update_support_status'
));

Route::any('update_support_comments', array(
    'uses' => 'App\Http\Controllers\SupportController@updateSupportComments',
    'as'   => 'update_support_comments'
));

Route::any('support_emails', array(
    'uses' => 'App\Http\Controllers\SupportController@supportEmails',
    'as'   => 'support_emails'
));
Route::any('email_json', array(
    'uses' => 'App\Http\Controllers\SupportController@emailJson',
    'as'   => 'email_json'
));

Route::any('view_ticket/{id}', array(
    'uses' => 'App\Http\Controllers\SupportController@viewTicket',
    'as'   => 'view_ticket'
));

Route::any('view_ticket_json', array(
    'uses' => 'App\Http\Controllers\SupportController@viewTicketJson',
    'as'   => 'view_ticket_json'
));

Route::any('change_ticket_domain', array(
    'uses' => 'App\Http\Controllers\SupportController@changeTicketDomain',
    'as'   => 'view_ticket'
));


Route::any('ticket_details', array(
    'uses' => 'App\Http\Controllers\SupportController@addTicketDetails',
    'as'   => 'ticket_details'
));

Route::any('update_details', array(
    'uses' => 'App\Http\Controllers\SupportController@updateDetails',
    'as'   => 'update_details'
));

Route::any('timesheets', array(
    'uses' => 'App\Http\Controllers\TimesheetController@timesheets',
    'as'   => 'timesheets'
));

Route::any('timesheet_details', array(
    'uses' => 'App\Http\Controllers\TimesheetController@addTimesheetDetails',
    'as'   => 'timesheet_details'
));

Route::any('projects', array(
    'uses' => 'App\Http\Controllers\ProjectController@Project',
    'as'   => 'projects'
));

Route::any('add_project', array(
    'uses' => 'App\Http\Controllers\ProjectController@addProject',
    'as'   => 'add_project'
));

Route::any('project_json', array(
    'uses' => 'App\Http\Controllers\ProjectController@projectjson',
    'as'   => 'project_json'
));

Route::any('add_ticket', array(
    'uses' => 'App\Http\Controllers\SupportController@addTicket',
    'as'   => 'add_ticket'
));

Route::any('timesheets_list', array(
    'uses' => 'App\Http\Controllers\TimesheetController@timesheetsList',
    'as'   => 'timesheets_list'
));

Route::any('timesheets_json', array(
    'uses' => 'App\Http\Controllers\TimesheetController@timesheetsJson',
    'as'   => 'timesheets_json'
));

Route::any('user_domain', array(
    'uses' => 'App\Http\Controllers\UserController@userDomain',
    'as'   => 'user_domain'
));

Route::any('add_domain', array(
    'uses' => 'App\Http\Controllers\UserController@addDomain',
    'as'   => 'add_domain'
));

Route::any('domain_json', array(
    'uses' => 'App\Http\Controllers\UserController@domainjson',
    'as'   => 'domain_json'
));

Route::any('get_domain/{user_id}', array(
    'uses' => 'App\Http\Controllers\UserController@getDomain',
    'as'   => 'get_domain'
));

Route::any('update_user_domain', array(
    'uses' => 'App\Http\Controllers\UserController@updateUserDomain',
    'as'   => 'update_user_domain'
));
Route::any('delete_domain/{id}/{status}', array(
    'uses' => 'App\Http\Controllers\UserController@deleteDomain',
    'as'   => 'delete_domain'
));

Route::any('company_details', array(
    'uses' => 'App\Http\Controllers\ProjectController@CompanyDetails',
    'as'   => 'company_details'
));
Route::any('edit_company_details/{id}', array(
    'uses' => 'App\Http\Controllers\ProjectController@editCompanyDetails',
    'as'   => 'edit_company_details'
));
Route::put('update_company_details/{id}', array(
    'uses' => 'App\Http\Controllers\ProjectController@updateCompanyDetails',
    'as'   => 'update_company_details'
));
Route::any('edit_primary_contact/{id}', array(
    'uses' => 'App\Http\Controllers\ProjectController@editPrimaryContact',
    'as'   => 'edit_primary_contact'
));
Route::put('update_primary_contact/{id}', array(
    'uses' => 'App\Http\Controllers\ProjectController@updatePrimaryContact',
    'as'   => 'update_primary_contact'
));
Route::any('edit_sub_contacts/{id}', array(
    'uses' => 'App\Http\Controllers\ProjectController@editSubContacts',
    'as'   => 'edit_sub_contacts'
));
Route::put('update_sub_contacts/{id}', array(
    'uses' => 'App\Http\Controllers\ProjectController@updateSubContacts',
    'as'   => 'update_sub_contacts'
));

Route::any('delete_customer/{id}/{status}', array(
    'uses' => 'App\Http\Controllers\ProjectController@deleteCustomer',
    'as'   => 'delete_customer'
));

Route::any('view_customer/{id}', array(
    'uses' => 'App\Http\Controllers\ProjectController@viewCustomerDetails',
    'as'   => 'view_customer'
));
Route::any('get_customer/{id}', array(
    'uses' => 'App\Http\Controllers\ProjectController@getCustomer',
    'as'   => 'get_customer'
));

Route::any('update_customer', array(
    'uses' => 'App\Http\Controllers\ProjectController@updateCustomer',
    'as'   => 'update_customer'
));

Route::any('get_status_loop/{ticket_id}', array(
    'uses' => 'App\Http\Controllers\SupportController@getStatus',
    'as'   => 'get_status_loop'
));

Route::any('get_timesheet_description/{id}', array(
    'uses' => 'App\Http\Controllers\TimesheetController@getDescription',
    'as'   => 'get_timesheet_description'
));


Route::get('/changePassword', [App\Http\Controllers\UserController::class, 'showChangePasswordGet'])->name('changePasswordGet');
Route::post('/changePassword', [App\Http\Controllers\UserController::class, 'changePasswordPost'])->name('changePasswordPost');
Route::post('replyticket', [App\Http\Controllers\SupportController::class, 'storeReplyTicket'])->name('replyticket');

Route::get('report', [App\Http\Controllers\ReportController::class, 'index'])->name('time.report');
Route::get('report_json', [App\Http\Controllers\ReportController::class, 'reportJson'])->name('report.json');
Route::get('get_user_timelog/{userId}/{month}/{year}', [App\Http\Controllers\ReportController::class, 'getUserTimelog'])->name('time.log');


Route::get('gmailread', [GmailController::class, 'index']);


Route::get('/send-test-email', function () {
    $recipientEmail = 'thananjeyan.g@tekroi.com';

    $data = [
        'message' => 'This is a test email from Support test!',
    ];

    Mail::send([], $data, function ($message) use ($recipientEmail) {
        $message->to($recipientEmail)
                ->subject('Test Email');
    });

    return 'Test email sent!';
});


Route::get('/birthday',[BirthdayController::class,'birthday'])->name('birthday');
Route::get('/birthdayform',[BirthdayController::class,'birthdayform'])->name('birthdayform');
Route::post('/birthdaydetails',[BirthdayController::class,'employeeformpost'])->name('employeeformpost');
Route::get('/edit-birthday/{id}/edit',[BirthdayController::class,'employeeformpostedit'])->name('employeeformpostedit');
Route::put('/employeeformpost/{id}/put',[BirthdayController::class,'employeeformpostput'])->name('employeeformpostput');
Route::delete('/employeeformpost/{id}/delete',[BirthdayController::class,'employeeformpostdelete'])->name('employeeformpostdelete')->withTrashed();
