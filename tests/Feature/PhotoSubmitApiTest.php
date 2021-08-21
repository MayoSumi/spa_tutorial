<?php

namespace Tests\Feature;

use Faker\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Photo;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class PhotoSubmitApiTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    /**
     * 画像のアップロードからローカルストレージへの保存
     * @group photo_upload
     */
    public function testCanUploadPhoto(): void
    {
        // テスト用にローカルストレージを使用する
        Storage::fake('s3');

        $response = $this->actingAs($this->user)
            ->json('POST', route('photo.create'), [
                // ダミーファイルを作成して送信
                'photo' => UploadedFile::fake()->image('photo.jpg'),
            ]);

        // レスポンスが201(CREATED)であること
        $response->assertStatus(201);

        $photo = Photo::first();

        // 写真のIDが12桁のランダムな文字列であること
        $this->assertRegExp('/^[0-9a-zA-Z-_]{12}$/', $photo->id);

        // ストレージにファイルが保存されていること
        Storage::cloud()->assertExists($photo->filename);
    }

    /**
     * DBエラー時はファイルを保存しない
     * @group photo_upload
     */
    public function testStopUploadingPhoto(): void
    {
        // 擬似DBエラー
        Schema::drop('photos');

        Storage::fake('s3');

        $response = $this->actingAs($this->user)
            ->json('POST', route('photo.create'), [
                'photo' => UploadedFile::fake()->image('photo.jpg'),
            ]);

        // レスポンスが500(INTERNAL SERVER ERROR)であること
        $response->assertStatus(500);

        // ストレージにファイルが保存されていないこと
        $this->assertCount(0, Storage::cloud()->files());
    }

    /**
     * ファイル保存エラー時はファイルを保存しない
     * @group photo_upload
     */
    public function testStopSavingPhoto(): void
    {
        // ストレージをモックして保存時にエラーを起こさせる
        Storage::shouldReceive('cloud')
            ->once()
            ->andReturnNull();

        $response = $this->actingAs($this->user)
            ->json('POST', route('photo.create'), [
                'photo' => UploadedFile::fake()->image('photo.jpg')
            ]);

        // レスポンスが500(INTERNAL SERVER ERROR)であること
        $response->assertStatus(500);

        // データベースに何も挿入されていないこと
        $this->assertEmpty(Photo::all());
    }
}
