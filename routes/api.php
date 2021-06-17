<?php


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

Route::get('/user','UserController@getAuthUser');

//___________________reset password create token_______________________________-
Route::post('password/create', 'PasswordResetController@create');


//_________________________Authentication Users____________________________________

Route::post('/login', 'AuthController@login');
Route::post('/register', 'AuthController@register');
Route::middleware('auth:api')->post('/logout', 'AuthController@logout');
Route::post('/confirm_email', 'AuthController@confirmEmail');


Route::resource('/questions', 'QuestionsController');
Route::resource('/answers', 'AnswersController')->middleware('auth:api');
Route::resource('/votes', 'VotesController')->middleware('auth:api');
Route::post('/questions/predict','QuestionsController@predictTopics');

Route::get('/topics/search', 'TopicsController@search');
Route::resource('/topics', 'TopicsController');
// Route::get('/topics', 'TopicsController@index');


Route::get('/topic_questions', 'QuestionsController@getQuestionsByTopic');
Route::get('/unanswered_questions', 'QuestionsController@unAnsweredQuestions');

//________________________________invite user to answer on a question_____________________________
Route::get('/users', 'UserRequestsController@getUsers');
Route::post('/invite_users', 'UserRequestsController@inviteUsers')->middleware('auth:api');
Route::get('/notifications', 'UserRequestsController@getNotifications')->middleware('auth:api');
Route::post('/readNotification', 'UserRequestsController@markAsRead')->middleware('auth:api');
Route::get('/topic_users', 'UserController@getUserByTopic');



Route::resource('/credentials', 'CredentialsController')->middleware('auth:api');
Route::delete('/user/{id}', 'AdminController@deleteUser')->middleware(['auth:api', 'authAdmin']);
Route::post('/report','ReportsController@store')->middleware('auth:api');
Route::get('/reports','ReportsController@index')->middleware(['auth:api','authAdmin']);
Route::put('/ignore_reports_on_question','ReportsController@ignoreQuestion')->middleware(['auth:api','authAdmin']);
Route::put('/ignore_reports_on_answer','ReportsController@ignoreAnswer')->middleware(['auth:api','authAdmin']);

//_________________________________admin api routes___________________________________
Route::group(['middleware'=>['auth:api','authAdmin'],'prefix'=>'/admin/'],function (){
    Route::group(['prefix'=>'dashboard/'],function (){
        Route::get('options', 'AdminController@getOptions');
        Route::get('questionsDetails', 'AdminController@getQuestionsDetails');
        Route::get('answersDetails', 'AdminController@getAnswersDetails');
        Route::get('topActiveUsers', 'AdminController@getTopActiveUsers');
        Route::get('topContributors', 'AdminController@getTopContributors');
    });
    Route::get('topics', 'TopicsController@getTopicsForAdmin');
    Route::get('checkAuth','AdminController@checkIsAdmin');
});

//________________________________profile api routes___________________________________
Route::group(['prefix' => 'users/'], function () {
    Route::put('', 'UserController@update')->middleware('auth:api');
    Route::get('{userId}', 'UserController@show');
    Route::get('{userId}/questions', 'UserController@userQuestions');
    Route::get('{userId}/answers', 'UserController@userAnswers');
    Route::put('{userId}/change_password', 'UserController@changeMyPassword');
});
