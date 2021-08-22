<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StorePhoto;
use App\Models\Photo;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Exception;
use Illuminate\Support\Facades\Auth;

class PhotoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * 写真投稿
     * @param StorePhoto $request
     * @return Response
     * @throws Exception
     */
    public function create(StorePhoto $request)
    {
        $extension = $request->photo->extension();

        $photo = new Photo();

        // ランダムなID値と拡張子を連結したものをファイル名に指定
        $photo->filename = $photo->id . '.' . $extension;

        Storage::cloud()
            ->putFileAs('', $request->photo, $photo->filename, 'public');

        DB::beginTransaction();

        try {
            Auth::user()->photos()->save($photo);
            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();

            Storage::cloud()->delete($photo->filename);
            throw $exception;
        }

        // リソースの新規作成のためレスポンスコード201(CREATED)を返却する
        return \response($photo, 201);
    }
}
