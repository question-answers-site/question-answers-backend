<?php

namespace App\Providers;

use App\Answer;
use App\Credential;
use App\Policies\AnswerPolicy;
use App\Policies\CredentialPolicy;
use App\Policies\QuestionPolicy;
use App\Question;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
//        'App\Model' => 'App\Policies\ModelPolicy',
        Question::class => QuestionPolicy::class,
        Answer::class => AnswerPolicy::class,
        Credential::class => CredentialPolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        Passport::routes(function ($router) {
            $router->forAccessTokens();
        });
        Passport::tokensExpireIn(Carbon::now()->addDays(15));
        Passport::refreshTokensExpireIn(Carbon::now()->addDays(30));

        //
    }
}
