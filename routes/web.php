<?php

// Increase memory limit to handle pages with poorly optimized queries
ini_set('memory_limit','500M');

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

if (env('APP_ENV') != 'production') {
    Route::get('/phpinfo', 'PHPInfoController@index');
}

Route::get('/', 'executive\AdminLoginController@showLoginForm');
Auth::routes();

// Export PDF route
Route::any('export', 'ExportPDFController')
    ->name('export');

Route::get('/resetpassword/{token}', 'Auth\ResetPasswordController@resetPassword')->name('resetpassword');
Route::post('/resetpassword', 'Auth\ResetPasswordController@resetPasswordSubmit')->name('resetpassword_submit');
//wix employee
Route::get('/wix-employeeportfolio/{id}', 'EmployeePortfolioController@wixindex');
Route::get('/wix-employeeportfolio/{id}/{startmonth}/{endmonth}', 'EmployeePortfolioController@wixdateindex');

//employee portfolio route

Route::post('/employeeportfolio/getbuildsfromsubcatid/{emp_id}/{id}/{start}/{end}', 'EmployeePortfolioController@get_BuildsfromsubcatID');
Route::post('/employeeportfolio/getbuildsfromsubcatid/{emp_id}/{id}/{start}/{end}/{search_text}', 'EmployeePortfolioController@get_BuildsfromsubcatIDSearch');
// independent main cat selection in portfolio page.
Route::post('/employeeportfolio/getbuildsfromcatidforindependent/{emp_id}/{id}/{start}/{end}', 'EmployeePortfolioController@get_BuildsfromcatIDForIndependent');
//
Route::post('/employeeportfolio/getChallengeImage/{emp_id}/{id}', 'EmployeePortfolioController@getchallengeimage');
Route::post('/employeeportfolio/getbuildinfofromid/{id}/{subcat_id}/{start}/{end}', 'EmployeePortfolioController@get_BuildinfofromID');
// for independent
Route::post('/employeeportfolio/getbuildinfofromidforindependent/{id}/{subcat_id}/{start}/{end}', 'EmployeePortfolioController@get_BuildinfofromIDForIndependent');
//
Route::post('/employeeportfolio/getbuildinfofromid/{id}/{subcat_id}/{start}/{end}/{search_text}', 'EmployeePortfolioController@get_BuildinfofromIDSearch');
Route::post('/employeeportfolio/getchallengeinfofromid/{id}/{type}/{emp_id}/{start}/{end}', 'EmployeePortfolioController@get_ChallengeinfofromID');
Route::post('/employeeportfolio/getchallengeinfofromid/{id}/{type}/{emp_id}/{start}/{end}/{search_text}', 'EmployeePortfolioController@get_ChallengeinfofromIDSearch');
Route::get('/employeeportfolio/{id}/{startmonth}/{endmonth}', 'EmployeePortfolioController@dateindex')->name('employeeportfolio.employeeportfolio.dateindex');
Route::get('/employeeportfolioIndependent/{id}/{startmonth}/{endmonth}', 'EmployeePortfolioController@dateindexIndependent')->name('resume.employeeportfolioIndependent.employeeportfolioIndependent.dateindexIndependent');
Route::get('/employeeportfolio/{id}/{startmonth}/{endmonth}/{category}', 'EmployeePortfolioController@cat_dateindex');

Route::get('/employeeportfolioIndependent/{id}/{startmonth}/{endmonth}/{category}', 'EmployeePortfolioController@cat_dateindexIndependent');

Route::get('/pdf', 'EmployeePortfolioController@makepdfbydate')->name('employeeportfolio.makepdf');
Route::post('/employeeportfolio/getchallengeimageByDate/{emp_id}/{id}/{start}/{end}', 'EmployeePortfolioController@getchallengeimageByDate');
Route::post('/employeeportfolio/getchallengeimageByDate/{emp_id}/{id}/{start}/{end}/{search_text}', 'EmployeePortfolioController@getchallengeimageByDateSearch');
Route::post('/employeeportfolio/getbuildsfromtestinomial/{cur_emp_id}/{testinomial_emp_id}/{status}/{start}/{end}', 'EmployeePortfolioController@getbuildsfromtestinomial');
Route::post('/employeeportfolio/getbuildsfromtestinomialbysearch/{cur_emp_id}/{testinomial_emp_id}/{status}/{start}/{end}/{search_text}', 'EmployeePortfolioController@getbuildsfromtestinomialbysearch');
Route::post('/employeeportfolio/getbuildsfromtestinomialbycategory/{cur_emp_id}/{testinomial_emp_id}/{status}/{start}/{end}/{category}', 'EmployeePortfolioController@getbuildsfromtestinomialbycategory');
Route::post('/employeeportfolio/getbuildinfofromtestinomialdata/{cur_emp_id}/{testinomial_emp_id}/{status}/{validate_id}/{start}/{end}', 'EmployeePortfolioController@getbuildinfofromtestinomialdata');
Route::post('/employeeportfolio/getbuildinfofromtestinomialdatabysearch/{cur_emp_id}/{testinomial_emp_id}/{status}/{validate_id}/{start}/{end}/{search_text}', 'EmployeePortfolioController@getbuildinfofromtestinomialdatabysearch');
Route::post('/employeeportfolio/getbuildinfofromtestinomialdatabycategory/{cur_emp_id}/{testinomial_emp_id}/{status}/{validate_id}/{start}/{end}/{category}', 'EmployeePortfolioController@getbuildinfofromtestinomialdatabycategory');

//pdf generator route

Route::post('/pdfgenerator/getbuildsfromsubcatid/{emp_id}/{id}/{start}/{end}', 'PDFGeneratorController@get_BuildsfromsubcatID');
Route::post('/pdfgenerator/getbuildsfromsubcatid/{emp_id}/{id}/{start}/{end}/{search_text}', 'PDFGeneratorController@get_BuildsfromsubcatIDSearch');
Route::post('/pdfgenerator/getChallengeImage/{emp_id}/{id}', 'PDFGeneratorController@getchallengeimage');
Route::post('/pdfgenerator/getbuildinfofromid/{id}/{subcat_id}/{start}/{end}', 'PDFGeneratorController@get_BuildinfofromID');
Route::post('/pdfgenerator/getbuildinfofromid/{id}/{subcat_id}/{start}/{end}/{search_text}', 'PDFGeneratorController@get_BuildinfofromIDSearch');
Route::post('/pdfgenerator/getchallengeinfofromid/{id}/{type}/{emp_id}/{start}/{end}', 'PDFGeneratorController@get_ChallengeinfofromID');
Route::post('/pdfgenerator/getchallengeinfofromid/{id}/{type}/{emp_id}/{start}/{end}/{search_text}', 'PDFGeneratorController@get_ChallengeinfofromIDSearch');

Route::get('/pdfgenerator/{id}/{startmonth}/{endmonth}', 'PDFGeneratorController@dateindex')->name('pdfgenerator.employeeportfolio.dateindex');
Route::get('/pdfgeneratorIndependent/{id}/{startmonth}/{endmonth}', 'PDFGeneratorController@dateindexIndependent')->name('pdfgenerator.employeeportfolio.dateindex');

Route::get('/pdfgenerator/{id}/{startmonth}/{endmonth}/{category}', 'PDFGeneratorController@cat_dateindex');
Route::get('/pdf', 'PDFGeneratorController@makepdfbydate')->name('pdfgenerator.makepdf');
Route::post('/pdfgenerator/getchallengeimageByDate/{emp_id}/{id}/{start}/{end}', 'PDFGeneratorController@getchallengeimageByDate');
Route::post('/pdfgenerator/getchallengeimageByDate/{emp_id}/{id}/{start}/{end}/{search_text}', 'PDFGeneratorController@getchallengeimageByDateSearch');
Route::post('/pdfgenerator/getbuildsfromtestinomial/{cur_emp_id}/{testinomial_emp_id}/{status}/{start}/{end}', 'PDFGeneratorController@getbuildsfromtestinomial');
Route::post('/pdfgenerator/getbuildsfromtestinomialbysearch/{cur_emp_id}/{testinomial_emp_id}/{status}/{start}/{end}/{search_text}', 'PDFGeneratorController@getbuildsfromtestinomialbysearch');
Route::post('/pdfgenerator/getbuildsfromtestinomialbycategory/{cur_emp_id}/{testinomial_emp_id}/{status}/{start}/{end}/{category}', 'PDFGeneratorController@getbuildsfromtestinomialbycategory');
Route::post('/pdfgenerator/getbuildinfofromtestinomialdata/{cur_emp_id}/{testinomial_emp_id}/{status}/{validate_id}/{start}/{end}', 'PDFGeneratorController@getbuildinfofromtestinomialdata');
Route::post('/pdfgenerator/getbuildinfofromtestinomialdatabysearch/{cur_emp_id}/{testinomial_emp_id}/{status}/{validate_id}/{start}/{end}/{search_text}', 'PDFGeneratorController@getbuildinfofromtestinomialdatabysearch');
Route::post('/pdfgenerator/getbuildinfofromtestinomialdatabycategory/{cur_emp_id}/{testinomial_emp_id}/{status}/{validate_id}/{start}/{end}/{category}', 'PDFGeneratorController@getbuildinfofromtestinomialdatabycategory');

Route::post('/employee/getEmployees/','executive\EmployeeController@getEmployees')->name('employees.getEmployees');

Route::prefix('executive')->group(function () {

    Route::get('/employee/save-device-token/{token}/{user_id}','executive\EmployeeloginController@saveDeviceToken')->name('save-device.token');

    /*The ROute for the Test(employeeView)*/
    //Route::get('/test/{id}','executive\TestController@index');

    Route::post('/test/getbuildsfromsubcatid/{emp_id}/{id}/{start}/{end}', 'executive\TestController@get_BuildsfromsubcatID');
    Route::post('/test/getbuildsfromsubcatid/{emp_id}/{id}/{start}/{end}/{search_text}', 'executive\TestController@get_BuildsfromsubcatIDSearch');

    Route::post('/test/getChallengeImage/{emp_id}/{id}', 'executive\TestController@getchallengeimage');

    Route::post('/test/getbuildinfofromid/{id}/{subcat_id}/{start}/{end}', 'executive\TestController@get_BuildinfofromID');

    Route::post('/test/getbuildinfofromid/{id}/{subcat_id}/{start}/{end}/{search_text}', 'executive\TestController@get_BuildinfofromIDSearch');

    Route::post('/test/getchallengeinfofromid/{id}/{type}/{emp_id}/{start}/{end}', 'executive\TestController@get_ChallengeinfofromID');

    Route::post('/test/getchallengeinfofromid/{id}/{type}/{emp_id}/{start}/{end}/{search_text}', 'executive\TestController@get_ChallengeinfofromIDSearch');
    Route::get('/test/{id}/{startmonth}/{endmonth}', 'executive\TestController@dateindex')
        ->middleware(['auth:service', 'auth:admin'])
        ->name('executive.employeeportfolio.dateindex');

    Route::get('/testdateIndependent/{id}/{startmonth}/{endmonth}', 'executive\TestController@dateindexIndependent')
        ->middleware(['auth:service', 'auth:admin'])
        ->name('employeeportfolioIndependent.employeeportfolioIndependent.dateindexIndependent');

    Route::get('/test/{id}/{startmonth}/{endmonth}/{category}', 'executive\TestController@cat_dateindex');
    // Route::get('/pdf/{id}','executive\TestController@makepdf');
    Route::get('/pdf', 'executive\TestController@makepdfbydate')->name('executive.makepdf');

    Route::post('/test/getchallengeimageByDate/{emp_id}/{id}/{start}/{end}', 'executive\TestController@getchallengeimageByDate');

    Route::post('/test/getchallengeimageByDate/{emp_id}/{id}/{start}/{end}/{search_text}', 'executive\TestController@getchallengeimageByDateSearch');

    Route::post('/test/getbuildsfromtestinomial/{cur_emp_id}/{testinomial_emp_id}/{status}/{start}/{end}', 'executive\TestController@getbuildsfromtestinomial');
    Route::post('/test/getbuildsfromtestinomialbysearch/{cur_emp_id}/{testinomial_emp_id}/{status}/{start}/{end}/{search_text}', 'executive\TestController@getbuildsfromtestinomialbysearch');
    Route::post('/test/getbuildsfromtestinomialbycategory/{cur_emp_id}/{testinomial_emp_id}/{status}/{start}/{end}/{category}', 'executive\TestController@getbuildsfromtestinomialbycategory');

    Route::post('/test/getbuildinfofromtestinomialdata/{cur_emp_id}/{testinomial_emp_id}/{status}/{validate_id}/{start}/{end}', 'executive\TestController@getbuildinfofromtestinomialdata');
    Route::post('/test/getbuildinfofromtestinomialdatabysearch/{cur_emp_id}/{testinomial_emp_id}/{status}/{validate_id}/{start}/{end}/{search_text}', 'executive\TestController@getbuildinfofromtestinomialdatabysearch');
    Route::post('/test/getbuildinfofromtestinomialdatabycategory/{cur_emp_id}/{testinomial_emp_id}/{status}/{validate_id}/{start}/{end}/{category}', 'executive\TestController@getbuildinfofromtestinomialdatabycategory');

    Route::get('/login', 'executive\AdminLoginController@showLoginForm')->name('executive.login');
    Route::post('/login', 'executive\AdminLoginController@login')->name('executive.login.submit');
    Route::get('/logout', 'executive\Employee\LoginController@logout')->name('executive.logout');

    // Old dashboard route. Deprecated and to be be removed soon.
    Route::get('/dashboard-old', 'executive\EmployeeloginController@home')->middleware(['auth:admin']);

    // New dashboard route
    Route::get('/dashboard', 'executive\DashboardController')
        ->middleware(['auth:service', 'auth:admin'])
        ->name('executive.dashboard');

/* The Route For the Employee */
    Route::get('/employee/list', 'executive\EmployeeloginController@employeelist')->middleware(['auth:admin'])->name('executive.employee.list');
    Route::get('/employee/useremployeedatatable', 'executive\EmployeeloginController@employeedatatable')->middleware(['auth:admin'])->name('executive.employee.useremployeedatatable');
    Route::get('/employee/employeecreate', 'executive\EmployeeloginController@employeecreate')->middleware(['auth:admin'])->name('executive.employee.employeecreate');
    Route::post('/employee/employeestore', 'executive\EmployeeloginController@employeestore')->middleware(['auth:admin'])->name('executive.employee.employeestore');
    Route::post('/employee/employeedelete', 'executive\EmployeeController@employeedelete')->middleware(['auth:admin'])->name('executive.employee.employeedelete');
    Route::get('/employee/showuser/{id}', 'executive\EmployeeloginController@showuser');
    Route::get('/employee/{id}/edit', 'executive\EmployeeloginController@editemployee');
    Route::post('/employee/{id}/employeeupdate', 'executive\EmployeeloginController@employeeupdate');
    Route::get('/employee/delete/{id}', 'executive\EmployeeController@delete');
/////////////////////////////////////////////////////////
    //add part
    Route::get('/employee/get-resume/{id}', 'executive\EmployeeController@getResume');
    Route::get('/employee/downloadresume/{key}', 'executive\HomeController@Downloadresume');
    Route::get('/employee/status/{id}/{status}', 'executive\BuildsController@employeeStatusData');
    Route::get('/builds/employeestatusajax/{id}/{status}', 'executive\BuildsController@employeeBuildByWinLoseData');
    Route::get('/builds/employee/{id}', 'executive\BuildsController@employeeBuild');
    Route::get('/builds/employee-data/{id}', 'executive\BuildsController@employeeBuildData');
    Route::get('/validations/employee/{id}', 'executive\ValidationsController@employeeValidations');
    Route::post('/validations/get-employee/{id}', 'executive\ValidationsController@getEmployee');
    Route::get('/validations/employee-data/{id}', 'executive\ValidationsController@employeeValidationsData');
    Route::get('/employee/company/{id}', 'executive\EmployeeloginController@company');
    Route::get('/employee/company-data/{id}', 'executive\EmployeeloginController@companyData');

    Route::get('/users', 'executive\UsersController@index')->name('executive.users.list');
    Route::get('/users/create', 'executive\UsersController@create');
    Route::post('/users/store', 'executive\UsersController@store');
    Route::get('/users/userdatatable', 'executive\UsersController@userdatatable');
    Route::get('/users/{id}', 'executive\UsersController@show');
    Route::get('/users/{id}/edit', 'executive\UsersController@edit');
    Route::post('/users/update/{id}', 'executive\UsersController@update');
    Route::get('/users/delete/{id}', 'executive\UsersController@delete');
    Route::post('/users/change-password', 'executive\UsersController@changePassword');

    Route::get('/tenure', 'executive\TenureController@index')->middleware(['auth:admin'])->name('executive.tenure.list');
    Route::get('/tenure/tenuredatatable', 'executive\TenureController@tenuredatatable');
    Route::post('/tenure/tenuredelete', 'executive\TenureController@tenureDelete');
    Route::get('/tenure/employee/{id}', 'executive\TenureController@employeeTenure');
    Route::get('/tenure/employee-data/{id}', 'executive\TenureController@employeeTenureData');
    Route::resource('/tenure', 'executive\TenureController');

    Route::get('/validation', 'executive\ValidationsController@index')->middleware(['auth:admin'])->name('executive.validation.list');
    Route::get('/validations/validationsdatatable', 'executive\ValidationsController@validationsdatatable')->middleware(['auth:admin'])->name('executive.validation.validationsdatatable');
    Route::get('/validations/create', 'executive\ValidationsController@create')->middleware(['auth:admin'])->name('executive.validation.create');
    Route::post('/validations/get-employee/{id}', 'executive\ValidationsController@getEmployee');
    Route::post('/validations/store', 'executive\ValidationsController@store');
    Route::post('/validations/validationdelete', 'executive\ValidationsController@validationdelete');
    Route::get('/validations/delete/{id}', 'executive\ValidationsController@delete');
    Route::get('/builds', 'executive\BuildsController@index')->middleware(['auth:admin'])->name('executive.builds.list');
    Route::get('/builds/buildsdatatable', 'executive\BuildsController@buildsdatatable')->middleware(['auth:admin'])->name('executive.builds.buildsdatatable');
    Route::get('/builds/create', 'executive\BuildsController@create')->middleware(['auth:admin'])->name('executive.builds.create');
    Route::post('/builds/get-category-from-employee/{id}', 'executive\BuildsController@getCategoryFromEmployee');
    Route::post('/builds/get-challenge-from-category-and-employee/{build_id}/{emp_id}/{cat_id}', 'executive\BuildsController@getChallengeFromEmployeeAndCategory');
    Route::post('/builds/store', 'executive\BuildsController@store')->middleware(['auth:admin'])->name('executive.builds.store');
    Route::post('/builds/builddelete', 'executive\BuildsController@builddelete')->middleware(['auth:admin'])->name('executive.builds.builddelete');
    Route::get('/builds/{id}', 'executive\BuildsController@show')->middleware(['auth:admin'])->name('executive.builds.show');
    Route::get('/builds/{id}/edit', 'executive\BuildsController@edit');
    Route::post('/builds/update/{id}', 'executive\BuildsController@update')->middleware(['auth:admin'])->name('executive.builds.update');
    Route::get('/builds/delete/{id}', 'executive\BuildsController@delete');

//////////////////////////////////////////////////////////////
    /*Route for the Industry */
    Route::get('/employee/industry', 'executive\EmployeeIndustryController@index')->middleware(['auth:admin'])->name('executive.industry.list');
    Route::get('/employee/industry/industrydatatable', 'executive\EmployeeIndustryController@industrydatatable')->middleware(['auth:admin'])->name('executive.employee.industrydatatable');
    Route::get('/employee/industry/create', 'executive\EmployeeIndustryController@create')->middleware(['auth:admin'])->name('executive.employee.industrycreate');
    Route::post('/employee/industry/store', 'executive\EmployeeIndustryController@store')->middleware(['auth:admin'])->name('executive.employee.industrystore');
    Route::get('/employee/industry/{id}/edit', 'executive\EmployeeIndustryController@edit')->middleware(['auth:admin'])->name('executive.employee.industryedit');
    Route::post('/employee/industry/{id}/update', 'executive\EmployeeIndustryController@update')->middleware(['auth:admin'])->name('executive.employee.industryupdate');
    Route::get('/employee/industry/delete/{id}', 'executive\EmployeeIndustryController@delete')->middleware(['auth:admin'])->name('executive.employee.industrydelete');

/*Route for the sub category */
    Route::get('/employee/categories', 'executive\EmployeeloginController@categories')->middleware(['auth:admin'])->name('executive.categories.list');
    Route::get('/employee/categories/categorydatatable', 'executive\EmployeeloginController@categoriesdatatable')->middleware(['auth:admin'])->name('executive.categories.categorydatatable');
    Route::get('/employee/categories/create', 'executive\EmployeeloginController@addcategory')->middleware(['auth:admin'])->name('executive.employee.categories.create');
    Route::post('/employee/categories/store', 'executive\EmployeeloginController@storecategory')->middleware(['auth:admin'])->name('executive.employee.categories.store');
    Route::get('/employee/categories/edit/{id}', 'executive\EmployeeloginController@editcategory')->middleware(['auth:admin'])->name('executive.employee.categories.edit');
    Route::post('/employee/categories/update/{id}', 'executive\EmployeeloginController@updatecategory')->middleware(['auth:admin'])->name('executive.employee.categories.update');
    Route::get('/employee/categories/delete/{id}', 'executive\EmployeeloginController@deletecategory')->middleware(['auth:admin'])->name('executive.employee.categories.deletecategory');
    Route::post('/employee/categorydelete', 'executive\EmployeeloginController@deletecategories')->middleware(['auth:admin'])->name('executive.employee.deletecategories');

/*Route for the level-challenge */
    Route::get('/level-challenge', 'executive\LevelChallengeController@index')->middleware(['auth:admin'])->name('executive.level-challenge.list');
    Route::get('/employee/level-challenge/challengedatatable', 'executive\LevelChallengeController@challengedatatable')->middleware(['auth:admin'])->name('executive.level-challenge.datatable');
    Route::get('/employee/level-challenge/create', 'executive\LevelChallengeController@create')->middleware(['auth:admin'])->name('executive.level-challenge.create');

    Route::post('/employee/level-challenge/getregion/{id}', 'executive\LevelChallengeController@getregion');
    Route::post('/employee/level-challenge/getaccesslevel', 'executive\LevelChallengeController@getaccesslevel');
    Route::post('/employee/level-challenge/getemployee/{region}/{level}', 'executive\LevelChallengeController@getemployee');
    Route::post('/employee/level-preset-challenge/getsubcategory/{id}', 'executive\LevelChallengeController@getSubcategory');

    Route::post('/employee/level-challenge/store', 'executive\LevelChallengeController@store')->middleware(['auth:admin'])->name('executive.level-challenge.store');
    Route::post('/employee/level-challenge/challangedelete', 'executive\LevelChallengeController@challangedelete')->middleware(['auth:admin'])->name('executive.level-challenge.challangedelete');
    Route::get('/employee/level-challenge/{id}', 'executive\LevelChallengeController@show')->middleware(['auth:admin'])->name('executive.level-challenge.show');
    Route::get('/employee/level-challenge/delete/{id}', 'executive\LevelChallengeController@delete');
    Route::get('/employee/level-challenge/{id}/edit', 'executive\LevelChallengeController@edit');
    Route::post('/employee/level-challenge/update/{id}', 'executive\LevelChallengeController@update')->middleware(['auth:admin'])->name('executive.level-challenge.update');
    Route::get('/employee/level-preset-challenge/getsubcategory', 'executive\LevelChallengeController@edit');

/*Route for the preset challenge */
    Route::get('/level-preset-challenge', 'executive\LevelPresetChallengeController@index')->middleware(['auth:admin'])->name('executive.level-preset-challenge.list');
    Route::get('/employee/level-preset-challenge/challengedatatable', 'executive\LevelPresetChallengeController@challengedatatable');
    Route::get('/employee/level-preset-challenge/create', 'executive\LevelPresetChallengeController@create')->middleware(['auth:admin'])->name('executive.level-preset-challenge.create');
    Route::post('/employee/level-preset-challenge/store', 'executive\LevelPresetChallengeController@store')->middleware(['auth:admin'])->name('executive.level-preset-challenge.store');
    Route::post('/employee/level-preset-challenge/challangedelete', 'executive\LevelPresetChallengeController@challangedelete')->middleware(['auth:admin'])->name('executive.level-preset-challenge.challangedelete');
    Route::get('/employee/level-preset-challenge/{id}', 'executive\LevelPresetChallengeController@show')->middleware(['auth:admin'])->name('executive.level-challenge.show');
    Route::get('/employee/level-preset-challenge/delete/{id}', 'executive\LevelPresetChallengeController@delete');
    Route::get('/employee/level-preset-challenge/{id}/edit', 'executive\LevelPresetChallengeController@edit');
    Route::post('/employee/level-preset-challenge/update/{id}', 'executive\LevelPresetChallengeController@update')->middleware(['auth:admin'])->name('executive.level-challenge.update');

//push-notification - list
    Route::get('/notification', 'executive\UsersController@pushNotification');
    Route::post('/sendnotification', 'executive\UsersController@sendnotification');
    //Route::post('/users/get-notification-users/{key}','executive\UsersController@getNotificationUserList');
    Route::post('/users/get-notification-role/{key}', 'executive\UsersController@getNotificationRoleList');
    Route::post('/users/get-access-level-users', 'executive\UsersController@getaccessleveluser');
    Route::post('/users/get-notification-employee/{region}/{level}', 'executive\UsersController@getNotificationEmployeeList');

/*Route for the build requests*/
    Route::get('/build_request', 'executive\BuildRequest@index');
    Route::get('/build_request/buildrequestdatatable', 'executive\BuildRequest@buildrequestdatatable');
    Route::get('/build_request/reject/{id}', 'executive\BuildRequest@reject');
    Route::get('/build_request/approve/{id}', 'executive\BuildRequest@approve');

/*Route for the employee requests*/
    Route::get('/employee_request', 'executive\EmployeeRequest@index');
    Route::get('/employee_requests/employeerequestdatatable', 'executive\EmployeeRequest@employeerequestdatatable');
    Route::get('/employee_requests/show/{id}', 'executive\EmployeeRequest@show');
    Route::get('/employee_requests/reject/{id}', 'executive\EmployeeRequest@delete');
    Route::get('/employee_requests/approve/{id}', 'executive\EmployeeRequest@approve');

/*Route for the challenge request */
    Route::get('/challengerequest', 'executive\EmployeeloginController@challengerequest')->middleware(['auth:admin'])->name('executive.challengerequests.list');
    Route::get('/employee/challenge requests/challengerequestdatatable', 'executive\EmployeeloginController@challengerequestdatatable')->middleware(['auth:admin'])->name('executive.challengerequests.challengerequestdatatable');
    Route::get('/employee/approve-request-challenge/{id}/{action}', 'executive\EmployeeloginController@handlechallengerequest');

/*Route for the Build */
    Route::get('/build', 'executive\EmployeeloginController@build')->middleware(['auth:admin'])->name('executive.builds.list');
    Route::get('/employee/build/buildsdatatable', 'executive\EmployeeloginController@buildsdatatable')->middleware(['auth:admin'])->name('executive.builds.buildsdatatable');
    Route::get('/employee/build/create', 'executive\EmployeeloginController@createbuild')->middleware(['auth:admin'])->name('executive.employee.builds.createbuild');
    Route::post('/employee/build/store', 'executive\EmployeeloginController@storebuild')->middleware(['auth:admin'])->name('executive.employee.builds.storebuild');
    Route::post('/employee/builds/builddelete', 'executive\BuildsController@builddelete')->middleware(['auth:admin'])->name('executive.employee.builds.builddelete');
    Route::get('/employee/buildshow/{id}', 'executive\EmployeeloginController@buildshow')->middleware(['auth:admin'])->name('executive.employee.builds.show');
    Route::get('/employee/edit-build/{id}', 'executive\EmployeeloginController@editbuild');
    Route::post('/employee/build/updatebuild', 'executive\EmployeeloginController@updatebuild')->middleware(['auth:admin'])->name('executive.employee.builds.updatebuild');
    Route::get('/employee/builds/delete/{id}', 'executive\EmployeeloginController@builddelete');

//announcement Pages
    Route::get('/upload', 'executive\UploadPageController@index');
    Route::get('/upload/uploaddatatable', 'executive\UploadPageController@uploaddatatable');
    Route::get('/upload/create', 'executive\UploadPageController@create');
    Route::post('/upload/store', 'executive\UploadPageController@store');
    Route::get('/upload/{id}/edit', 'executive\UploadPageController@edit');
    Route::post('/upload/{id}/update', 'executive\UploadPageController@update');
    Route::get('/upload/delete/{id}', 'executive\UploadPageController@destroy');
    Route::get('/upload/showuser/{id}', 'executive\UploadPageController@show');
    Route::post('/upload/deletes', 'executive\UploadPageController@deletes');
    Route::post('/upload/get_photoview_empcount/{region}/{id}', 'executive\UploadPageController@get_VisitCount_Photo');
    Route::resource('upload', 'executive\UploadPageController');

//Reward Pages
    Route::get('/reward', 'executive\RewardPageController@index');
    Route::get('/reward/rewarddatatable', 'executive\RewardPageController@rewarddatatable');
    Route::get('/reward/create', 'executive\RewardPageController@create');
    Route::post('/reward/store', 'executive\RewardPageController@store');
    Route::get('/reward/{id}/edit', 'executive\RewardPageController@edit');
    Route::post('/reward/{id}/update', 'executive\RewardPageController@update');
    Route::get('/reward/delete/{id}', 'executive\RewardPageController@destroy');
    Route::get('/reward/showuser/{id}', 'executive\RewardPageController@show');
    Route::post('/reward/deletes', 'executive\RewardPageController@deletes');
    Route::get('/reward/useremployeedatatable', 'executive\RewardPageController@useremployeedatatable');
    Route::get('/reward/showemployee/{id}', 'executive\EmployeeloginController@showuser');
    Route::delete('purchases/{id}', 'executive\RewardPageController@deletePurchase')->name('purchases.delete');
    Route::resource('reward', 'executive\RewardPageController');
/*The Route For the Tier */

    Route::get('/tier', 'executive\TierController@index')->middleware(['auth:admin']);

    Route::get('/tier/tierdatatable', 'executive\TierController@tierdatatable')->middleware(['auth:admin']);
    Route::get('/tier/create', 'executive\TierController@create')->middleware(['auth:admin']);
    Route::post('/tier/store', 'executive\TierController@store')->middleware(['auth:admin']);
    Route::post('/tier/update/{id}', 'executive\TierController@update')->middleware(['auth:admin']);
    Route::get('/tier/delete/{id}', 'executive\TierController@delete')->middleware(['auth:admin']);
    Route::post('/tier/destroy', 'executive\TierController@destroy')->middleware(['auth:admin']);
    Route::post('/tier/getsubcategory/{level}', 'executive\TierController@getsubcategory')->middleware(['auth:admin']);
    Route::post('/tier/getsubcategoryfromid/{id}', 'executive\TierController@getsubcategoryfromid')->middleware(['auth:admin']);
    Route::get('/tier/{id}/edit', 'executive\TierController@edit');

});

/*Routes for Company user*/

Route::prefix('rep')->group(function () {

    Route::get('/dashboard', 'rep\EmployeeloginController@home')->middleware(['auth:admin'])->name('rep.dashboard');

/* The Route For the Employee */
    Route::get('/employee/list', 'rep\EmployeeloginController@employeelist')->middleware(['auth:admin'])->name('rep.employee.list');
    Route::get('/employee/useremployeedatatable', 'rep\EmployeeloginController@employeedatatable')->middleware(['auth:admin'])->name('rep.employee.useremployeedatatable');
    Route::get('/employee/employeecreate', 'rep\EmployeeloginController@employeecreate')->middleware(['auth:admin'])->name('rep.employee.employeecreate');
    Route::post('/employee/employeestore', 'rep\EmployeeloginController@employeestore')->middleware(['auth:admin'])->name('rep.employee.employeestore');
    Route::post('/employee/employeedelete', 'rep\EmployeeController@employeedelete')->middleware(['auth:admin'])->name('rep.employee.employeedelete');
    Route::get('/employee/showuser/{id}', 'rep\EmployeeloginController@showuser');
    Route::get('/employee/{id}/edit', 'rep\EmployeeloginController@editemployee');
    Route::post('/employee/{id}/employeeupdate', 'rep\EmployeeloginController@employeeupdate');
    Route::get('/employee/delete/{id}', 'rep\EmployeeController@delete');
/////////////////////////////////////////////////////////
    //add part
    Route::get('/employee/get-resume/{id}', 'rep\EmployeeController@getResume');
    Route::get('/employee/downloadresume/{key}', 'rep\HomeController@Downloadresume');
    Route::get('/employee/status/{id}/{status}', 'rep\BuildsController@employeeStatusData');
    Route::get('/builds/employeestatusajax/{id}/{status}', 'rep\BuildsController@employeeBuildByWinLoseData');
    Route::get('/builds/employee/{id}', 'rep\BuildsController@employeeBuild');
    Route::get('/builds/employee-data/{id}', 'rep\BuildsController@employeeBuildData');
    Route::get('/validations/employee/{id}', 'rep\ValidationsController@employeeValidations');
    Route::post('/validations/get-employee/{id}', 'rep\ValidationsController@getEmployee');
    Route::get('/validations/employee-data/{id}', 'rep\ValidationsController@employeeValidationsData');

    Route::get('/employee/company/{id}', 'rep\EmployeeloginController@company');
    Route::get('/employee/company-data/{id}', 'rep\EmployeeloginController@companyData');

    Route::get('/users/list', 'rep\UsersController@index')->name('rep.users.list');
    Route::get('/users/create', 'rep\UsersController@create');
    Route::post('/users/store', 'rep\UsersController@store');
    Route::get('/users/userdatatable', 'rep\UsersController@userdatatable');
    Route::get('/users/{id}', 'rep\UsersController@show');
    Route::get('/users/{id}/edit', 'rep\UsersController@edit');
    Route::post('/users/update/{id}', 'rep\UsersController@update');
    Route::get('/users/delete/{id}', 'rep\UsersController@delete');
    Route::post('/users/change-password', 'rep\UsersController@changePassword');

    Route::get('/tenure/list', 'rep\TenureController@index')->middleware(['auth:admin'])->name('rep.tenure.list');
    Route::get('/tenure/tenuredatatable', 'rep\TenureController@tenuredatatable');
    Route::post('/tenure/tenuredelete', 'rep\TenureController@tenureDelete');
    Route::get('/tenure/employee/{id}', 'rep\TenureController@employeeTenure');
    Route::get('/tenure/employee-data/{id}', 'rep\TenureController@employeeTenureData');
    Route::resource('/tenure', 'rep\TenureController');

    Route::get('/validation/list', 'rep\ValidationsController@index')->middleware(['auth:admin'])->name('rep.validation.list');
    Route::get('/validations/validationsdatatable', 'rep\ValidationsController@validationsdatatable')->middleware(['auth:admin'])->name('rep.validation.validationsdatatable');
    Route::get('/validations/create', 'rep\ValidationsController@create')->middleware(['auth:admin'])->name('rep.validation.create');
    Route::post('/validations/get-employee/{id}', 'rep\ValidationsController@getEmployee');
    Route::post('/validations/store', 'rep\ValidationsController@store');
    Route::post('/validations/validationdelete', 'rep\ValidationsController@validationdelete');
    Route::get('/validations/delete/{id}', 'rep\ValidationsController@delete');

//////////////////////////////////////////////////////////////
    /*Route for the Industry */
    Route::get('/employee/industry', 'rep\EmployeeIndustryController@index')->middleware(['auth:admin'])->name('rep.industry.list');
    Route::get('/employee/industry/industrydatatable', 'rep\EmployeeIndustryController@industrydatatable')->middleware(['auth:admin'])->name('rep.employee.industrydatatable');
    Route::get('/employee/industry/create', 'rep\EmployeeIndustryController@create')->middleware(['auth:admin'])->name('rep.employee.industrycreate');
    Route::post('/employee/industry/store', 'rep\EmployeeIndustryController@store')->middleware(['auth:admin'])->name('rep.employee.industrystore');
    Route::get('/employee/industry/{id}/edit', 'rep\EmployeeIndustryController@edit')->middleware(['auth:admin'])->name('rep.employee.industryedit');
    Route::post('/employee/industry/{id}/update', 'rep\EmployeeIndustryController@update')->middleware(['auth:admin'])->name('rep.employee.industryupdate');
    Route::get('/employee/industry/delete/{id}', 'rep\EmployeeIndustryController@delete')->middleware(['auth:admin'])->name('rep.employee.industrydelete');
/*Route for the sub category */
    Route::get('/employee/categories', 'rep\EmployeeloginController@categories')->middleware(['auth:admin'])->name('rep.categories.list');
    Route::get('/employee/categories/categorydatatable', 'rep\EmployeeloginController@categoriesdatatable')->middleware(['auth:admin'])->name('rep.categories.categorydatatable');
    Route::get('/employee/categories/create', 'rep\EmployeeloginController@addcategory')->middleware(['auth:admin'])->name('rep.employee.categories.create');
    Route::post('/employee/categories/store', 'rep\EmployeeloginController@storecategory')->middleware(['auth:admin'])->name('rep.employee.categories.store');
    Route::get('/employee/categories/edit/{id}', 'rep\EmployeeloginController@editcategory')->middleware(['auth:admin'])->name('rep.employee.categories.edit');
    Route::post('/employee/categories/update/{id}', 'rep\EmployeeloginController@updatecategory')->middleware(['auth:admin'])->name('rep.employee.categories.update');
    Route::get('/employee/categories/delete/{id}', 'rep\EmployeeloginController@deletecategory')->middleware(['auth:admin'])->name('rep.employee.categories.deletecategory');
/*Route for the Build */
    Route::get('/employee/build', 'rep\EmployeeloginController@build')->middleware(['auth:admin'])->name('rep.builds.list');
    Route::get('/employee/build/buildsdatatable', 'rep\EmployeeloginController@buildsdatatable')->middleware(['auth:admin'])->name('rep.builds.buildsdatatable');
    Route::get('/employee/build/create', 'rep\EmployeeloginController@createbuild')->middleware(['auth:admin'])->name('rep.employee.builds.createbuild');
    Route::post('/employee/build/store', 'rep\EmployeeloginController@storebuild')->middleware(['auth:admin'])->name('rep.employee.builds.storebuild');
    Route::post('/employee/builds/builddelete', 'rep\BuildsController@builddelete')->middleware(['auth:admin'])->name('rep.employee.builds.builddelete');
    Route::get('/employee/buildshow/{id}', 'rep\EmployeeloginController@buildshow')->middleware(['auth:admin'])->name('rep.employee.builds.show');
    Route::get('/employee/edit-build/{id}', 'rep\EmployeeloginController@editbuild');
    Route::post('/employee/build/updatebuild', 'rep\EmployeeloginController@updatebuild')->middleware(['auth:admin'])->name('rep.employee.builds.updatebuild');
    Route::get('/employee/builds/delete/{id}', 'rep\EmployeeloginController@builddelete');
});

/*Route for the administrator */
Route::prefix('master')->group(function () {
    Route::get('/dashboard', 'master\HomeController@index')->middleware(['auth:admin'])->name('master.dashboard');

    /*The ROute for the Test(employeeView)*/
    // Route::get('/test/{id}','master\TestController@index');
    Route::post('/test/getbuildsfromsubcatid/{emp_id}/{id}/{start}/{end}', 'master\TestController@get_BuildsfromsubcatID');
    Route::post('/test/getbuildsfromsubcatid/{emp_id}/{id}/{start}/{end}/{search_text}', 'master\TestController@get_BuildsfromsubcatIDSearch');
    Route::post('/test/getChallengeImage/{emp_id}/{id}', 'master\TestController@getchallengeimage');

    Route::post('/test/getbuildinfofromid/{id}/{subcat_id}/{start}/{end}', 'master\TestController@get_BuildinfofromID');
    Route::post('/test/getbuildinfofromid/{id}/{subcat_id}/{start}/{end}/{search_text}', 'master\TestController@get_BuildinfofromIDSearch');

    Route::post('/test/getchallengeinfofromid/{id}/{type}/{emp_id}/{start}/{end}', 'master\TestController@get_ChallengeinfofromID');
    Route::post('/test/getchallengeinfofromid/{id}/{type}/{emp_id}/{start}/{end}/{search_text}', 'master\TestController@get_ChallengeinfofromIDSearch');

//  This Route is for the date range button
    Route::get('/check', 'master\TestController@checktest');

    // Route::get('/test/{id}/{startdate}/{enddate}','master\TestController@dateindex');
    Route::get('/test/{id}/{startmonth}/{endmonth}', 'master\TestController@dateindex')
        ->middleware(['auth:service', 'auth:admin'])
        ->name('master.employeeportfolio.dateindex');

    Route::get('/testdateIndependent/{id}/{startmonth}/{endmonth}', 'master\TestController@dateindexIndependent')
        ->middleware(['auth:service', 'auth:admin'])
        ->name('master.employeeportfolioIndependent.dateindexIndependent');

    Route::get('/test/{id}/{startmonth}/{endmonth}/{category}', 'master\TestController@cat_dateindex');
    Route::get('/testdateIndependent/{id}/{startmonth}/{endmonth}/{category}', 'master\TestController@cat_dateindexIndependent');

    //Route::get('/pdf/{id}','master\TestController@makepdf');
    Route::get('/pdf', 'master\TestController@makepdfbydate')->name('master.makepdf');
    Route::get('/pdfIndependent', 'master\TestController@makepdfbydateIndependent')->name('master.makepdfbydateIndependent');

    Route::post('/test/getchallengeimageByDate/{emp_id}/{id}/{start}/{end}', 'master\TestController@getchallengeimageByDate');
    Route::post('/test/getchallengeimageByDate/{emp_id}/{id}/{start}/{end}/{search_text}', 'master\TestController@getchallengeimageByDateSearch');

    Route::post('/test/getbuildsfromtestinomial/{cur_emp_id}/{testinomial_emp_id}/{status}/{start}/{end}', 'master\TestController@getbuildsfromtestinomial');
    Route::post('/test/getbuildsfromtestinomialbysearch/{cur_emp_id}/{testinomial_emp_id}/{status}/{start}/{end}/{search_text}', 'master\TestController@getbuildsfromtestinomialbysearch');
    Route::post('/test/getbuildsfromtestinomialbycategory/{cur_emp_id}/{testinomial_emp_id}/{status}/{start}/{end}/{category}', 'master\TestController@getbuildsfromtestinomialbycategory');

    Route::post('/test/getbuildinfofromtestinomialdata/{cur_emp_id}/{testinomial_emp_id}/{status}/{validate_id}/{start}/{end}', 'master\TestController@getbuildinfofromtestinomialdata');
    Route::post('/test/getbuildinfofromtestinomialdatabysearch/{cur_emp_id}/{testinomial_emp_id}/{status}/{validate_id}/{start}/{end}/{search_text}', 'master\TestController@getbuildinfofromtestinomialdatabysearch');
    Route::post('/test/getbuildinfofromtestinomialdatabycategory/{cur_emp_id}/{testinomial_emp_id}/{status}/{validate_id}/{start}/{end}/{category}', 'master\TestController@getbuildinfofromtestinomialdatabycategory');

/* The Route For the Employee */
    Route::get('/employee/list', 'master\EmployeeController@index')->middleware(['auth:admin'])->name('master.employee.list');
    Route::get('/employee/employeedatatable', 'master\EmployeeController@employeedatatable')->middleware(['auth:admin'])->name('master.employee.employeedatatable');
    Route::get('/employee/create', 'master\EmployeeController@create')->middleware(['auth:admin'])->name('master.employee.create');
    Route::post('/employee/get-industry-from-company/{id}', 'master\EmployeeController@getIndustryFromCompany');
    Route::post('/employee/store', 'master\EmployeeController@store')->middleware(['auth:admin'])->name('master.employee.store');
    Route::post('/employee/employeedelete', 'master\EmployeeController@employeedelete')->middleware(['auth:admin'])->name('master.employee.delete');
    Route::get('/employee/create', 'master\EmployeeController@create')->middleware(['auth:admin'])->name('master.employee.create');
    Route::get('/employee/{id}/useredit', 'master\EmployeeController@edit');
    Route::post('/employee/update/{id}', 'master\EmployeeController@update')->middleware(['auth:admin'])->name('master.employee.update');
    Route::get('/employee/delete/{id}', 'master\EmployeeController@delete')->middleware(['auth:admin'])->name('master.employee.delete');
    Route::get('/employee/restore/{id}', 'master\EmployeeController@restore')->middleware(['auth:admin'])->name('master.employee.restore');
//action
    Route::get('/employee/{id}', 'master\EmployeeController@show')->middleware(['auth:admin'])->name('master.employee.show');
    Route::get('/employee/get-resume/{id}', 'master\EmployeeController@getResume');
    Route::get('/employee/downloadresume/{key}', 'master\HomeController@Downloadresume');
    Route::get('/employee/status/{id}/{status}', 'master\BuildsController@employeeStatusData');
    Route::get('/builds/employeestatusajax/{id}/{status}', 'master\BuildsController@employeeBuildByWinLoseData');
    Route::get('/builds/employee/{id}', 'master\BuildsController@employeeBuild');
    Route::get('/builds/employee-data/{id}', 'master\BuildsController@employeeBuildData');
    Route::get('/validations/employee/{id}', 'master\ValidationsController@employeeValidations');
    Route::post('/validations/get-employee/{id}', 'master\ValidationsController@getEmployee');
    Route::get('/validations/employee-data/{id}', 'master\ValidationsController@employeeValidationsData');
    Route::get('/employee/company/{id}', 'master\EmployeeController@company');
    Route::get('/employee/company-data/{id}', 'master\EmployeeController@companyData');
/*Route for the Industry */
    Route::get('/industry', 'master\IndustryController@index')->middleware(['auth:admin'])->name('master.industry.list');
    Route::get('/industry/industrydatatable', 'master\IndustryController@industrydatatable')->middleware(['auth:admin'])->name('master.industrydatatable');
    Route::get('/industry/create', 'master\IndustryController@create')->name('master.industrycreate');
    Route::post('/industry/store', 'master\IndustryController@store')->middleware(['auth:admin'])->name('master.industrystore');
    Route::get('/industry/{id}/edit', 'master\IndustryController@edit')->middleware(['auth:admin'])->name('master.industryedit');
    Route::post('/industry/{id}/update', 'master\IndustryController@update')->middleware(['auth:admin'])->name('master.industryupdate');
    Route::get('/industry/delete/{id}', 'master\IndustryController@delete')->middleware(['auth:admin'])->name('master.industrydelete');
/*Route for the main category */
    Route::get('/categories', 'master\CategoriesController@index')->middleware(['auth:admin'])->name('master.categories.list');
    Route::get('/categories/categoriesdatatable', 'master\CategoriesController@categoriesdatatable')->middleware(['auth:admin'])->name('master.categories.categoriesdatatable');
    Route::get('/categories/create', 'master\CategoriesController@create')->middleware(['auth:admin'])->name('master.categories.create');
    Route::post('/categories/store', 'master\CategoriesController@store')->middleware(['auth:admin'])->name('master.categories.store');
    Route::get('/categories/{id}/edit', 'master\CategoriesController@edit')->name('master.categories.edit');
    Route::post('/categories/update/{id}', 'master\CategoriesController@update')->middleware(['auth:admin'])->name('master.categories.update');
    Route::get('/categories/delete/{id}', 'master\CategoriesController@delete')->middleware(['auth:admin'])->name('master.categories.delete');
    Route::post('/categories/categorydelete', 'master\CategoriesController@deletecategories')->middleware(['auth:admin'])->name('executive.employee.deletecategories');
    Route::get('/categories/{id}/restore', 'master\CategoriesController@restore')->middleware(['auth:admin'])->name('master.categories.restore');

/*Route for the challenge */
    Route::get('/challenge', 'master\ChallengeController@index')->middleware(['auth:admin'])->name('master.challenge.list');
    Route::get('/challenge/challengedatatable', 'master\ChallengeController@challengedatatable');
    Route::get('/challenge/create', 'master\ChallengeController@create');
    Route::post('/challenge/get-cateory/{id}', 'master\ChallengeController@getCategoryFromCompany');
    Route::post('/challenge/getregion/{company}/{id}', 'master\ChallengeController@getRegion');
    Route::post('/challenge/getaccesslevel', 'master\ChallengeController@getaccesslevel');
    Route::post('/challenge/getsubcategory/{id}', 'master\ChallengeController@getSubcategory');
    Route::post('/challenge/getemployee/{company}/{region}/{level}', 'master\ChallengeController@getemployee');

    Route::post('/challenge/store', 'master\ChallengeController@store');
    Route::post('/challenge/challangedelete', 'master\ChallengeController@challangedelete');
    Route::get('/challenge/{id}', 'master\ChallengeController@show');
    Route::get('/challenge/{id}/edit', 'master\ChallengeController@edit');
    Route::post('/challenge/update/{id}', 'master\ChallengeController@update');
    Route::get('/challenge/delete/{id}', 'master\ChallengeController@delete');

/*Route for the preset challenge */
    Route::get('/preset-challenge', 'master\PresetChallengeController@index')->middleware(['auth:admin'])->name('master.preset-challenge.list');
    Route::get('/preset-challenge/challengedatatable', 'master\PresetChallengeController@challengedatatable');
    Route::get('/preset-challenge/create', 'master\PresetChallengeController@create');
    Route::post('/preset-challenge/store', 'master\PresetChallengeController@store');
    Route::post('/preset-challenge/challangedelete', 'master\PresetChallengeController@challangedelete');
    Route::get('/preset-challenge/{id}', 'master\PresetChallengeController@show');
    Route::get('/preset-challenge/delete/{id}', 'master\PresetChallengeController@delete');
    Route::get('/preset-challenge/{id}/edit', 'master\PresetChallengeController@edit');
    Route::post('/preset-challenge/update/{id}', 'master\PresetChallengeController@update');

/*Route for the Build */
    Route::get('/main/builds', 'master\BuildsController@index')->middleware(['auth:admin'])->name('master.builds.list');
    Route::get('/builds/buildsdatatable', 'master\BuildsController@buildsdatatable')->middleware(['auth:admin'])->name('master.builds.buildsdatatable');
    Route::get('/builds/create', 'master\BuildsController@create')->middleware(['auth:admin'])->name('master.builds.create');
    Route::post('/builds/get-category-from-employee/{id}', 'master\BuildsController@getCategoryFromEmployee');
    Route::post('/builds/get-challenge-from-category-and-employee/{build_id}/{emp_id}/{cat_id}', 'master\BuildsController@getChallengeFromEmployeeAndCategory');
    Route::post('/builds/store', 'master\BuildsController@store')->middleware(['auth:admin'])->name('master.builds.store');
    Route::post('/builds/builddelete', 'master\BuildsController@builddelete')->middleware(['auth:admin'])->name('master.builds.builddelete');
    Route::get('/builds/{id}', 'master\BuildsController@show')->middleware(['auth:admin'])->name('master.builds.show');
    Route::get('/builds/{id}/edit', 'master\BuildsController@edit');
    Route::post('/builds/update/{id}', 'master\BuildsController@update')->middleware(['auth:admin'])->name('master.builds.update');
    Route::get('/builds/delete/{id}', 'master\BuildsController@delete');
    Route::post('/build/getsubcategory/{id}', 'master\BuildsController@getSubcategory');
    //validation
    Route::get('/validation', 'master\ValidationsController@index')->middleware(['auth:admin'])->name('master.validation.list');
    Route::get('/validations/validationsdatatable', 'master\ValidationsController@validationsdatatable')->middleware(['auth:admin'])->name('master.validation.validationsdatatable');
    Route::get('/validations/create', 'master\ValidationsController@create')->middleware(['auth:admin'])->name('master.validation.create');
    Route::post('/validations/get-employee/{id}', 'master\ValidationsController@getEmployee');
    Route::post('/validations/store', 'master\ValidationsController@store');
    Route::post('/validations/validationdelete', 'master\ValidationsController@validationdelete');
    Route::get('/validations/delete/{id}', 'master\ValidationsController@delete');
    //Routing For tenure
    Route::get('/tenure', 'master\TenureController@index')->middleware(['auth:admin'])->name('master.tenure.list');
    Route::get('/tenure/tenuredatatable', 'master\TenureController@tenuredatatable');
    Route::post('/tenure/tenuredelete', 'master\TenureController@tenureDelete');
    Route::get('/tenure/employee/{id}', 'master\TenureController@employeeTenure');
    Route::get('/tenure/employee-data/{id}', 'master\TenureController@employeeTenureData');
    Route::resource('/tenure', 'master\TenureController');
//push-notification - list
    Route::get('/notification', 'master\UsersController@pushNotification');
    Route::post('/sendnotification', 'master\UsersController@sendnotification');
    //Route::post('/get-notification-company/{key}','master\UsersController@getNotificationCompany');
    //Route::post('/get-notification-region/{company}','master\UsersController@getNotificationRegion');
    //Route::post('/get-notification-level','master\UsersController@getNotificationLevel');
    //Route::post('/get-notification-employeelist/{company}/{region}/{level}','master\UsersController@getNotificationEmployeeList');
    Route::post('/users/get-notification-role/{key}', 'master\UsersController@getNotificationRole');
    Route::post('/users/getregionbycompany/{company}', 'master\UsersController@getregionbycompany');
    Route::post('/users/getlevelbycompany', 'master\UsersController@getlevelbycompany');
    Route::post('/users/getregion/{company}', 'master\UsersController@getregion');
    Route::post('/users/get-access-level-users', 'master\UsersController@getaccessleveluser');
    Route::post('/users/get-notification-employee/{company}/{region}/{level}', 'master\UsersController@getNotificationEmployeeList');

//route for the users controller
    Route::get('/company', 'master\UsersController@index')->name('master.users.list');
    Route::get('/users/create', 'master\UsersController@create');
    Route::post('/users/store', 'master\UsersController@store');
    Route::get('/users/userdatatable', 'master\UsersController@userdatatable');
    Route::get('/users/{id}', 'master\UsersController@show');
    Route::get('/users/{id}/edit', 'master\UsersController@edit');
    Route::post('/users/update/{id}', 'master\UsersController@update');
    Route::get('/users/delete/{id}', 'master\UsersController@delete');
    Route::post('/users/change-password', 'master\UsersController@changePassword');
    Route::get('/employee/show/{id}', 'master\EmployeeController@show');

});

Route::prefix('leader')->group(function () {
    Route::get('/dashboard', 'leader\EmployeeloginController@home')->middleware(['auth:admin'])->name('leader.dashboard');
/* The Route For the Employee */
    Route::get('/employee/list', 'leader\EmployeeloginController@employeelist')->middleware(['auth:admin'])->name('leader.employee.list');
    Route::get('/employee/useremployeedatatable', 'leader\EmployeeloginController@employeedatatable')->middleware(['auth:admin'])->name('leader.employee.useremployeedatatable');
    Route::get('/employee/employeecreate', 'leader\EmployeeloginController@employeecreate')->middleware(['auth:admin'])->name('leader.employee.employeecreate');
    Route::post('/employee/employeestore', 'leader\EmployeeloginController@employeestore')->middleware(['auth:admin'])->name('leader.employee.employeestore');
    Route::post('/employee/employeedelete', 'leader\EmployeeController@employeedelete')->middleware(['auth:admin'])->name('leader.employee.employeedelete');
    Route::get('/employee/showuser/{id}', 'leader\EmployeeloginController@showuser')->name('leader.employee.showuserdetails');
    Route::get('/employee/{id}/edit', 'leader\EmployeeloginController@editemployee');
    Route::post('/employee/{id}/employeeupdate', 'leader\EmployeeloginController@employeeupdate');
    Route::get('/employee/delete/{id}', 'leader\EmployeeController@delete');
/////////////////////////////////////////////////////////

//add part
    Route::get('/employee/get-resume/{id}', 'leader\EmployeeController@getResume');
    Route::get('/employee/downloadresume/{key}', 'leader\HomeController@Downloadresume');
    Route::get('/employee/status/{id}/{status}', 'leader\BuildsController@employeeStatusData');
    Route::get('/builds/employeestatusajax/{id}/{status}', 'leader\BuildsController@employeeBuildByWinLoseData');
    Route::get('/builds/employee/{id}', 'leader\BuildsController@employeeBuild');
    Route::get('/builds/employee-data/{id}', 'leader\BuildsController@employeeBuildData');
    Route::get('/validations/employee/{id}', 'leader\ValidationsController@employeeValidations');
    Route::post('/validations/get-employee/{id}', 'leader\ValidationsController@getEmployee');
    Route::get('/validations/employee-data/{id}', 'leader\ValidationsController@employeeValidationsData');
    Route::get('/employee/company/{id}', 'leader\EmployeeloginController@company');
    Route::get('/employee/company-data/{id}', 'leader\EmployeeloginController@companyData');

    Route::get('/users', 'leader\UsersController@index')->name('leader.users.list');
    Route::get('/users/create', 'leader\UsersController@create');
    Route::post('/users/store', 'leader\UsersController@store');
    Route::get('/users/userdatatable', 'leader\UsersController@userdatatable');
    Route::get('/users/{id}', 'leader\UsersController@show');
    Route::get('/users/{id}/edit', 'leader\UsersController@edit');
    Route::post('/users/update/{id}', 'leader\UsersController@update');
    Route::get('/users/delete/{id}', 'leader\UsersController@delete');
    Route::post('/users/change-password', 'leader\UsersController@changePassword');

    Route::get('/tenure', 'leader\TenureController@index')->middleware(['auth:admin'])->name('leader.tenure.list');
    Route::get('/tenure/tenuredatatable', 'leader\TenureController@tenuredatatable');
    Route::post('/tenure/tenuredelete', 'leader\TenureController@tenureDelete');
    Route::get('/tenure/employee/{id}', 'leader\TenureController@employeeTenure');
    Route::get('/tenure/employee-data/{id}', 'leader\TenureController@employeeTenureData');
    Route::resource('/tenure', 'leader\TenureController');

    Route::get('/validation', 'leader\ValidationsController@index')->middleware(['auth:admin'])->name('leader.validation.list');
    Route::get('/validations/validationsdatatable', 'leader\ValidationsController@validationsdatatable')->middleware(['auth:admin'])->name('leader.validation.validationsdatatable');
    Route::get('/validations/create', 'leader\ValidationsController@create')->middleware(['auth:admin'])->name('leader.validation.create');
    Route::post('/validations/get-employee/{id}', 'leader\ValidationsController@getEmployee');
    Route::post('/validations/store', 'leader\ValidationsController@store');
    Route::post('/validations/validationdelete', 'leader\ValidationsController@validationdelete');
    Route::get('/validations/delete/{id}', 'leader\ValidationsController@delete');

    Route::get('/builds', 'leader\BuildsController@index')->middleware(['auth:admin'])->name('leader.builds.list');
    Route::get('/builds/buildsdatatable', 'leader\BuildsController@buildsdatatable')->middleware(['auth:admin'])->name('leader.builds.buildsdatatable');
    Route::get('/builds/create', 'leader\BuildsController@create')->middleware(['auth:admin'])->name('leader.builds.create');
    Route::post('/builds/get-category-from-employee/{id}', 'leader\BuildsController@getCategoryFromEmployee');
    Route::post('/builds/get-challenge-from-category-and-employee/{build_id}/{emp_id}/{cat_id}', 'leader\BuildsController@getChallengeFromEmployeeAndCategory');
    Route::post('/builds/store', 'leader\BuildsController@store')->middleware(['auth:admin'])->name('leader.builds.store');
    Route::post('/builds/builddelete', 'leader\BuildsController@builddelete')->middleware(['auth:admin'])->name('leader.builds.builddelete');
    Route::get('/builds/{id}', 'leader\BuildsController@show')->middleware(['auth:admin'])->name('leader.builds.show');
    Route::get('/builds/{id}/edit', 'leader\BuildsController@edit');
    Route::post('/builds/update/{id}', 'leader\BuildsController@update')->middleware(['auth:admin'])->name('leader.builds.update');
    Route::get('/builds/delete/{id}', 'leader\BuildsController@delete');

//////////////////////////////////////////////////////////////

/*Route for the sub category */
    Route::get('/categories', 'leader\EmployeeloginController@categories')->middleware(['auth:admin'])->name('leader.categories.list');
    Route::get('/employee/categories/categorydatatable', 'leader\EmployeeloginController@categoriesdatatable')->middleware(['auth:admin'])->name('leader.categories.categorydatatable');
    Route::get('/employee/categories/create', 'leader\EmployeeloginController@addcategory')->middleware(['auth:admin'])->name('leader.employee.categories.create');
    Route::post('/employee/categories/store', 'leader\EmployeeloginController@storecategory')->middleware(['auth:admin'])->name('leader.employee.categories.store');
    Route::get('/employee/categories/edit/{id}', 'leader\EmployeeloginController@editcategory')->middleware(['auth:admin'])->name('leader.employee.categories.edit');
    Route::post('/employee/categories/update/{id}', 'leader\EmployeeloginController@updatecategory')->middleware(['auth:admin'])->name('leader.employee.categories.update');
    Route::get('/employee/categories/delete/{id}', 'leader\EmployeeloginController@deletecategory')->middleware(['auth:admin'])->name('leader.employee.categories.deletecategory');

/*Route for the level-challenge */
    Route::get('/level-challenge', 'leader\LevelChallengeController@index')->middleware(['auth:admin'])->name('leader.level-challenge.list');
    Route::get('/employee/level-challenge/challengedatatable', 'leader\LevelChallengeController@challengedatatable')->middleware(['auth:admin'])->name('leader.level-challenge.datatable');
    Route::get('/employee/level-challenge/create', 'leader\LevelChallengeController@create')->middleware(['auth:admin'])->name('leader.level-challenge.create');

    Route::post('/employee/level-challenge/getregion/{id}', 'leader\LevelChallengeController@getregion');
    Route::post('/employee/level-challenge/getaccesslevel', 'leader\LevelChallengeController@getaccesslevel');
    Route::post('/employee/level-challenge/getaccesslevelbyregion', 'leader\LevelChallengeController@getaccesslevelbyregion');

    Route::post('/employee/level-challenge/getemployee/{region}/{level}', 'leader\LevelChallengeController@getemployee');

    Route::post('/employee/level-challenge/getemployee/{id}', 'leader\LevelChallengeController@getemployee');
    Route::post('/employee/level-preset-challenge/getsubcategory/{id}', 'leader\LevelChallengeController@getSubcategory');
    Route::post('/employee/level-challenge/store', 'leader\LevelChallengeController@store')->middleware(['auth:admin'])->name('leader.level-challenge.store');
    Route::post('/employee/level-challenge/challangedelete', 'leader\LevelChallengeController@challangedelete')->middleware(['auth:admin'])->name('leader.level-challenge.challangedelete');
    Route::get('/employee/level-challenge/{id}', 'leader\LevelChallengeController@show')->middleware(['auth:admin'])->name('leader.level-challenge.show');
    Route::get('/employee/level-challenge/delete/{id}', 'leader\LevelChallengeController@delete');
    Route::get('/employee/level-challenge/{id}/edit', 'leader\LevelChallengeController@edit');
    Route::post('/employee/level-challenge/update/{id}', 'leader\LevelChallengeController@update')->middleware(['auth:admin'])->name('leader.level-challenge.update');
    Route::get('/employee/level-preset-challenge/getsubcategory', 'leader\LevelChallengeController@getSubcategory');

/*Route for the preset challenge */
    Route::get('/level-preset-challenge', 'leader\LevelPresetChallengeController@index')->middleware(['auth:admin'])->name('leader.level-preset-challenge.list');
    Route::get('/employee/level-preset-challenge/challengedatatable', 'leader\LevelPresetChallengeController@challengedatatable');
    Route::get('/employee/level-preset-challenge/create', 'leader\LevelPresetChallengeController@create')->middleware(['auth:admin'])->name('leader.level-preset-challenge.create');
    Route::post('/employee/level-preset-challenge/store', 'leader\LevelPresetChallengeController@store')->middleware(['auth:admin'])->name('leader.level-preset-challenge.store');
    Route::post('/employee/level-preset-challenge/challangedelete', 'leader\LevelPresetChallengeController@challangedelete')->middleware(['auth:admin'])->name('leader.level-preset-challenge.challangedelete');
    Route::get('/employee/level-preset-challenge/{id}', 'leader\LevelPresetChallengeController@show')->middleware(['auth:admin'])->name('leader.level-challenge.show');
    Route::get('/employee/level-preset-challenge/delete/{id}', 'leader\LevelPresetChallengeController@delete');
    Route::get('/employee/level-preset-challenge/{id}/edit', 'leader\LevelPresetChallengeController@edit');
    Route::post('/employee/level-preset-challenge/update/{id}', 'leader\LevelPresetChallengeController@update')->middleware(['auth:admin'])->name('leader.level-challenge.update');

/*Route for the Build */
    Route::get('/build', 'leader\EmployeeloginController@build')->middleware(['auth:admin'])->name('leader.build.list');
    Route::get('/employee/build/buildsdatatable', 'leader\EmployeeloginController@buildsdatatable')->middleware(['auth:admin'])->name('leader.build.buildsdatatable');
    Route::get('/employee/build/create', 'leader\EmployeeloginController@createbuild')->middleware(['auth:admin'])->name('leader.employee.builds.createbuild');
    Route::post('/employee/build/store', 'leader\EmployeeloginController@storebuild')->middleware(['auth:admin'])->name('leader.employee.builds.storebuild');
    Route::post('/employee/builds/builddelete', 'leader\BuildsController@builddelete')->middleware(['auth:admin'])->name('leader.employee.builds.builddelete');
    Route::get('/employee/buildshow/{id}', 'leader\EmployeeloginController@buildshow')->middleware(['auth:admin'])->name('leader.employee.builds.show');
    Route::get('/employee/edit-build/{id}', 'leader\EmployeeloginController@editbuild');
    Route::post('/employee/build/updatebuild', 'leader\EmployeeloginController@updatebuild')->middleware(['auth:admin'])->name('leader.employee.builds.updatebuild');
    Route::get('/employee/builds/delete/{id}', 'leader\EmployeeloginController@builddelete');

/*Route for the pushnotification */
    Route::get('/notification', 'leader\UsersController@pushNotification');
    Route::post('/sendnotification', 'leader\UsersController@sendnotification');
    //Route::post('/users/get-notification-users/{key}','leader\UsersController@getNotificationUserList');
    Route::post('/users/get-notification-role/{key}', 'leader\UsersController@getNotificationRoleList');
    Route::post('/users/get-access-level-users', 'leader\UsersController@getaccessleveluser');
    Route::post('/users/get-notification-employee/{region}/{level}', 'leader\UsersController@getNotificationEmployeeList');

//announcement Pages
    Route::get('/upload', 'leader\UploadPageController@index');
    Route::get('/upload/uploaddatatable', 'leader\UploadPageController@uploaddatatable');
    Route::get('/upload/create', 'leader\UploadPageController@create');
    Route::post('/upload/store', 'leader\UploadPageController@store');
    Route::get('/upload/{id}/edit', 'leader\UploadPageController@edit');
    Route::post('/upload/{id}/update', 'leader\UploadPageController@update');
    Route::get('/upload/delete/{id}', 'leader\UploadPageController@destroy');
    Route::get('/upload/showuser/{id}', 'leader\UploadPageController@show');
    Route::post('/upload/deletes', 'leader\UploadPageController@deletes');
    Route::post('/upload/get_photoview_empcount/{region}/{id}', 'leader\UploadPageController@get_VisitCount_Photo');
    Route::resource('upload', 'leader\UploadPageController');

//Reward Pages
    Route::get('/reward', 'leader\RewardPageController@index');
    Route::get('/reward/rewarddatatable', 'leader\RewardPageController@rewarddatatable');
    Route::get('/reward/create', 'leader\RewardPageController@create');
    Route::post('/reward/store', 'leader\RewardPageController@store');
    Route::get('/reward/{id}/edit', 'leader\RewardPageController@edit');
    Route::post('/reward/{id}/update', 'leader\RewardPageController@update');
    Route::get('/reward/delete/{id}', 'leader\RewardPageController@destroy');
    Route::get('/reward/showuser/{id}', 'leader\RewardPageController@show');
    Route::post('/reward/deletes', 'leader\RewardPageController@deletes');
    Route::get('/reward/useremployeedatatable', 'leader\RewardPageController@useremployeedatatable');
    Route::get('/reward/showemployee/{id}', 'leader\EmployeeloginController@showuser');
    Route::resource('reward', 'leader\RewardPageController');
/*The Route For the Tier */

    Route::get('/tier', 'leader\TierController@index')->middleware(['auth:admin']);

    Route::get('/tier/tierdatatable', 'leader\TierController@tierdatatable')->middleware(['auth:admin']);
    Route::get('/tier/create', 'leader\TierController@create')->middleware(['auth:admin']);
    Route::post('/tier/store', 'leader\TierController@store')->middleware(['auth:admin']);
    Route::post('/tier/update/{id}', 'leader\TierController@update')->middleware(['auth:admin']);
    Route::get('/tier/delete/{id}', 'leader\TierController@delete')->middleware(['auth:admin']);
    Route::post('/tier/destroy', 'leader\TierController@destroy')->middleware(['auth:admin']);
    Route::post('/tier/getsubcategory/{level}', 'leader\TierController@getsubcategory')->middleware(['auth:admin']);
    Route::post('/tier/getsubcategoryfromid/{id}', 'leader\TierController@getsubcategoryfromid')->middleware(['auth:admin']);
    Route::get('/tier/{id}/edit', 'leader\TierController@edit');
});

Route::pattern('uuid', '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}');

Route::prefix('verify_submission')->middleware(['prevent-back-history'])->group(function () {

    Route::get('/{uuid}', 'SubmitVerifyController@index')
        ->name('verify.submission');

    Route::get('/{uuid}/authenticate', 'SubmitVerifyController@authenticate')
        ->name('verify.submission.authenticate');

    Route::post('/{uuid}/authenticate', 'SubmitVerifyController@authenticate');
    Route::post('/{uuid}/approval', 'SubmitVerifyController@approval')
        ->name('verify.submission.approval');

    Route::post('/{uuid}/process_approval', 'SubmitVerifyController@process_approval')
        ->name('verify.submission.process');
    Route::get('/{uuid}/thank_you', 'SubmitVerifyController@thanks')
        ->name('verify.submission.thanks');
    Route::get('/{uuid}/submission_closed', 'SubmitVerifyController@submission_closed')
        ->name('verify.submission.closed');

    Route::get('/{uuid}/preview_image', 'SubmitVerifyController@preview_image')
        ->name('verify.submission.preview');

    Route::get('/{uuid}/share', 'SubmitVerifyController@share')
        ->name('verify.submission.share');
});

Route::post('/sendBookSMS', 'EmployeePortfolioController@sendSMSText');
Route::post('/sendViewerNameForWatchedEmployeePortfolioIndependent', 'EmployeePortfolioController@sendViewerNameForWatchedEmployeePortfolioIndependent');
Route::post('/sendContactText', 'EmployeePortfolioController@sendContactText');



Route::get('/employeeportfolioIndependentNew/{id}/{startmonth}/{endmonth}', 'EmployeePortfolioController@dateindexIndependentNew')->name('resume.employeeportfolioIndependent.employeeportfolioIndependent.dateindexIndependentNew');
