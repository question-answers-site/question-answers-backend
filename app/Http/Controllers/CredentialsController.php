<?php

namespace App\Http\Controllers;

use App\Credential;
use App\User;
use Illuminate\Http\Request;

class CredentialsController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request = [object body,string type]
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $credential = auth('api')->user()->credentials()->create([
            'body' => $request->body,
            'type' => $request->type
        ]);

        return response(['credential'=>$credential],200);
    }

    /**
     * Update the specified resource in storage
     * @param  \Illuminate\Http\Request  $request[] = [body , type]
     * @param  Credential  $id
     * @return \Illuminate\Http\Response $response[] = [credential after modified]
     */
    public function update(Request $request,Credential $credential)
    {
        $this->authorize('update',$credential);
        $credential->update([
            'body' => $request->body,
            'type' => $request->type
        ]);

        return response(['credential'=>$credential],200);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Credential $credential)
    {
        $this->authorize('delete',$credential);
        $credential->delete();
        return response('deleted successfully',200);
    }
}
