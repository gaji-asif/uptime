<?php

use Illuminate\Http\Request;
use App\Http\Middleware\APIAuth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('login', 'API\ApiController@userLogin');
Route::post('employeeLogin', 'API\ApiController@employeeLogin');

Route::get('getbitly/', 'API\ApiController@getBitly');
Route::post('/builds/skipApproval', 'API\ApiController@skipApproval')->middleware(APIAuth::class);


// This for Email Verification
Route::post('emailVerification', 'API\ApiController@emailVerification');

// This is for check email existance
Route::post('checkEmailExistance', 'API\ApiController@checkEmailExistance');

// This is for forgot password

Route::post('forgotPassword', 'API\ApiController@forgotPassword');
Route::post('forgotPasswordSave/', 'API\ApiController@forgotPasswordSave');

//
Route::post('setUUid', 'API\ApiController@setUUid')->middleware(APIAuth::class);
Route::post('employeeRegister', 'API\ApiController@employeeRegister');//->middleware(APIAuth::class)
Route::post('changepassword', 'API\ApiController@changePassword')->middleware(APIAuth::class);

// Route::post('buildpost', 'API\ApiController@buildPost')->middleware(APIAuth::class);
Route::post('buildpost', 'API\ApiController@buildPost');
Route::post('buildposts', 'API\ApiController@buildPosts')->middleware(APIAuth::class);

Route::post('reminderBuild', 'API\ApiController@reminderBuild');

Route::post('getRegion', 'API\ApiController@getRegion')->middleware(APIAuth::class);
Route::post('calculatepoints', 'API\ApiController@calculatePoints')->middleware(APIAuth::class);
Route::post('getcategories', 'API\ApiController@getCategories')->middleware(APIAuth::class);
Route::post('getPublicCategories', 'API\ApiController@getPublicCategories');//->middleware(APIAuth::class);
Route::post('getMainCategories', 'API\ApiController@getMainCategories')->middleware(APIAuth::class);
Route::post('getbuild', 'API\ApiController@getBuild')->middleware(APIAuth::class);
Route::post('getbuilds', 'API\ApiController@getBuilds')->middleware(APIAuth::class);
Route::post('postchallenge', 'API\ApiController@postChallenge')->middleware(APIAuth::class);
Route::post('getchallenge', 'API\ApiController@GetChallenge')->middleware(APIAuth::class);
Route::post('getchallenges', 'API\ApiController@GetChallenges')->middleware(APIAuth::class);
Route::post('postvalidate', 'API\ApiController@postValidate')->middleware(APIAuth::class);
Route::post('updateProfile', 'API\ApiController@updateProfile')->middleware(APIAuth::class);
Route::post('getvalidate', 'API\ApiController@getValidate')->middleware(APIAuth::class);
Route::post('getvalidates', 'API\ApiController@getValidates')->middleware(APIAuth::class);
Route::post('deleteprofile', 'API\ApiController@deleteProfile')->middleware(APIAuth::class);
Route::post('logoutemployee', 'API\ApiController@logoutEmployee')->middleware(APIAuth::class);
Route::post('gettenure', 'API\ApiController@getTenure');
Route::post('gettemployee', 'API\ApiController@getEmployee');
Route::post('gettemployeefromcompany', 'API\ApiController@getEmployeeFromCompany')->middleware(APIAuth::class);
Route::post('getresume', 'API\ApiController@getResume');
Route::get('getresumeapi', 'API\ApiController@getResumeApi');
Route::post('getallchallenges', 'API\ApiController@getAllchallenges');
Route::post('getallbuilds', 'API\ApiController@getAllbuilds')->middleware(APIAuth::class);
Route::post('getallcompanies', 'API\ApiController@getAllcompanies');
Route::post('getnotification', 'API\ApiController@getNotification')->middleware(APIAuth::class);

Route::post('mobilesendpush', 'API\ApiController@mobilesendpush')->middleware(APIAuth::class);

Route::post('gettenurebymonth', 'API\ApiController@getTenurebymonth')->middleware(APIAuth::class);
Route::post('deletebuild', 'API\ApiController@deleteBuild')->middleware(APIAuth::class);
Route::post('getchatusers', 'API\ApiController@getchatusers')->middleware(APIAuth::class);

Route::get('getcompanyinfo', 'API\ApiController@getCompanyInfo');
Route::get('getregionfromcompany','API\ApiController@getRegionfromCompany');

Route::post('addvisit','API\ApiController@addVisit');
Route::post('getallreaditems','API\ApiController@getAllReadItems')->middleware(APIAuth::class);
Route::post('getallrewards','API\ApiController@getAllRewards')->middleware(APIAuth::class);


Route::post('createnewduel','API\ApiController@createNewDuel')->middleware(APIAuth::class);
Route::post('acceptduel','API\ApiController@acceptDuel');
Route::post('rejectduel','API\ApiController@rejectDuel')->middleware(APIAuth::class);
Route::post('completeduel','API\ApiController@completeDuel');

Route::post('getpresetchallenges','API\ApiController@getPresetChallenges')->middleware(APIAuth::class);
Route::post('getallcompletedduels','API\ApiController@getAllCompletedDuels')->middleware(APIAuth::class);
Route::post('getduelrequests','API\ApiController@getDuelRequests')->middleware(APIAuth::class);
Route::post('getinprogressduels','API\ApiController@getInprogressDuels')->middleware(APIAuth::class);
Route::post('getpresetcount', 'API\ApiController@getPresetCount')->middleware(APIAuth::class);
Route::post('gettimedchallenges', 'API\ApiController@getTimedChallenges')->middleware(APIAuth::class);
Route::post('getbadge', 'API\ApiController@getBadge')->middleware(APIAuth::class);

Route::post('createpurchase', 'API\ApiController@createPurchase')->middleware(APIAuth::class);

Route::post('usersearch', 'API\ApiController@userSearch')->middleware(APIAuth::class);
Route::post('get_challenge_wincount', 'API\ApiController@get_Challenge_WinCount')->middleware(APIAuth::class);

Route::post('get_profile_details','API\ApiController@getProfileDetails')->middleware(APIAuth::class);
//Route::post('get_employee_tiermodel','API\ApiController@getEmployeeTierModel')->middleware(APIAuth::class);

Route::get('goMobilePortfolio','API\ApiController@goMobilePortfolio');
Route::get('goMobilePortfolioIndependent','API\ApiController@goMobilePortfolioIndependent');

Route::get('shareWebViewURLForIndependent','API\ApiController@shareWebViewURLForIndependent');
Route::get('share','API\ApiController@share');
Route::get('makePDFForMobile', 'API\ApiController@makePDFForMobile');

//new endpoint for get mobile portfolio without token just userId
Route::get('getMobilePortfolio/{id}','API\ApiController@getMobilePortfolio');
Route::get('checkPhpinfo','API\ApiController@checkPhpinfo');

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('qrcode', function (Request $request) {
	$src = '';
	if (isset($_SERVER['HTTP_REFERER']))
		$src = $_SERVER['HTTP_REFERER'];
	if (!empty($request->get('src')))
		$src = $request->get('src');
	if (empty($src))
		$src = 'https://www.uptimeprofile.com';
	header('Content-type: image/png');
    echo QrCode::format('png')->merge('/public/images/uptime-logo-qrcode.png', 0.16)
    	->size(300)->margin(0)->errorCorrection('H')->generate($src);
    die();
})->name('qrcode');