<?php

namespace App\Http\Controllers;

use App\Answer;
use Illuminate\Http\Request;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Mime\Part\Multipart\FormDataPart;

class AnswersController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $editorContent = $request->editorContent;
        $dom = new \DOMDocument('1.0', 'UTF-8');
        libxml_use_internal_errors(true);
        $dom->loadHtml($editorContent);
        //now you can save your editor content into the database with the variable $editor_content_save
        $questionId = $request->questionId;
        $userId = auth('api')->user()->id;

        $answer = Answer::create([
            'body' => 'nullll',
            'question_id' => $questionId,
            'user_id' => $userId
        ]);

        $images = $dom->getElementsByTagName('img');
        foreach ($images as $k => $img) {
            $image_data = $img->getAttribute('src');
            list($type, $data) = explode(';', $image_data); // exploding data for later checking and validating

            if (preg_match('/^data:image\/(\w+);base64,/', $image_data, $type)) {
                $data = substr($data, strpos($data, ',') + 1);
                $type = strtolower($type[1]); // jpg, png, gif
                if (!in_array($type, ['jpg', 'jpeg', 'gif', 'png'])) {
                    throw new \Exception('invalid image type');
                }
                $data = base64_decode($data);
                if ($data === false) {
                    throw new \Exception('base64_decode failed');
                }
            } else {
                throw new \Exception('did not match data URI with image data');
            }

            $fullname = 'answersImages/answer' . $answer->id . time() . $k . '.' . $type;

            Storage::put($fullname, $data);
            $answer->images()->create(['url' => 'storage/' . $fullname]);

            $img->removeAttribute('src');
            $img->setAttribute('src', url('/storage/' . $fullname));
            $img->setAttribute('style', 'display:block;max-height:400; max-width:100%;margin:0 auto');
        };
        $editorContentSave = utf8_decode($dom->saveHTML($dom));

        $answer->update(['body' => $editorContentSave]);
        return response($answer, 200);
    }


    /**
     * Update the specified resource in storage.
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id, Request $request)
    {
        $answer = Answer::findOrFail($id);
        $this->authorize('update', $answer);


        $editorContent = $request->answer_body;
        $dom = new \DOMDocument('1.0', 'UTF-8');
        libxml_use_internal_errors(true);
        $dom->loadHtml($editorContent);
        //now you can save your editor content into the database with the variable $editor_content_save
        $questionId = $request->questionId;
        $userId = auth('api')->user()->id;

        $images = $dom->getElementsByTagName('img');
        foreach ($images as $k => $img) {
            $image_data = $img->getAttribute('src');
            if (preg_match('/data:image/', $image_data)) {
                list($type, $data) = explode(';', $image_data); // exploding data for later checking and validating
                if (preg_match('/^data:image\/(\w+);base64,/', $image_data, $type)) {
                    $data = substr($data, strpos($data, ',') + 1);
                    $type = strtolower($type[1]); // jpg, png, gif
                    if (!in_array($type, ['jpg', 'jpeg', 'gif', 'png'])) {
                        throw new \Exception('invalid image type');
                    }
                    $data = base64_decode($data);
                    if ($data === false) {
                        throw new \Exception('base64_decode failed');
                    }
                } else {
                    throw new \Exception('did not match data URI with image data');
                }

                $fullname = 'answersImages/answer' . $answer->id . time() . $k . '.' . $type;
                Storage::put($fullname, $data);
                $answer->images()->create(['url' => 'storage/' . $fullname]);

                $img->removeAttribute('src');
                $img->setAttribute('src', url('/storage/' . $fullname));
                $img->setAttribute('style', 'display:block;height:400px; max-height:400px; max-width:100%;margin:0 auto');

            }
        };
        $editorContentSave = utf8_decode($dom->saveHTML($dom));

        $answer->update(['body' => $editorContentSave]);

        return response(['answer' => Answer::findOrFail($id)], 200);
    }


    /**
     * Remove the specified resource from storage.
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $answer = Answer::findOrFail($id);
        $this->authorize('delete',$answer);
        Cache::forget('unAnsweredQuestions');
        $answer->delete();
        return response('answer deleted successfully',200);
    }
}
