<?php

namespace App\Policies;

use App\User;
use App\Credential;
use Illuminate\Auth\Access\HandlesAuthorization;

class CredentialPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can update the credential.
     *
     * @param  \App\User  $user
     * @param  \App\Credential  $credential
     * @return mixed
     */

    public function update(User $user, Credential $credential)
    {
        //
        return $user->id == $credential->user_id;
    }

    /**
     * Determine whether the user can delete the credential.
     *
     * @param  \App\User  $user
     * @param  \App\Credential  $credential
     * @return mixed
     */
    public function delete(User $user, Credential $credential)
    {
        return $user->id == $credential->user_id;
    }

}
