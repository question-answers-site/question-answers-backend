<?php

namespace App\Http\Controllers;

use App\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class TopicsController extends Controller
{
    public function getTopicsForAdmin()
    {
        $topics =Topic::paginate(9);
        return response($topics, 200);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $searchValue = $request->searchValue;
        $topics = Topic::where('title', 'like', '%' . $searchValue . '%')->get();
        return response($topics, 200);
    }

    public function index()
    {
        //
        $topics = cache()->remember('topics', now()->addDay(), function () {
            return Topic::get();
        });


        return response($topics, 200);
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $topic = new Topic();
        $topic->title = $request->title;
        $topic->description = $request->description;
        $old = Topic::where('title', $topic->title)->get();
        if ($old) {
            return response('topic already exist', 500);
        }
        $topic->save();
        Cache::forget('topics');
//        $topic = Topic::create(['title' => $request->title, 'description' => $request->description]);
        return response($topic, 200);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $topic = Topic::findOrFail($id);
//        $this->authorize('update', $topic);
        $title = $request->title;
        $description = $request->description;
        Topic::whereId($id)->update(['title' => $title, 'description' => $description]);
        Cache::forget('topics');
        return response(Topic::findOrFail($id), 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $topic = Topic::findOrFail($id);
//        $this->authorize('delete', $topic);
        $topic = Topic::destroy($id);
        Cache::forget('topics');
        return response("Successfully Deleted", 200);
    }
}
