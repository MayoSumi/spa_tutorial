<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class Photo extends Model
{
    use HasFactory;

    /**
     * JSONに含める属性
     */
    protected $appends = [
        'url',
    ];

    /**
     * JSONに含めない属性
     */
    protected $visible = [
        'id',
        'owner',
        'url',
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * IDの桁数
     */
    public const ID_LENGTH = 12;

    /**
     * 写真作成時に自動でsetIdを呼び出す
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        if (! Arr::get($this->attributes, 'id')) {
            $this->setId();
        }
    }

    /**
     * ランダムなID値をid属性に代入する
     */
    public function setId()
    {
        $this->attributes['id'] = $this->getRandomId();
    }

    /**
     * ランダムなID値を生成する
     * @return string
     * @throws Exception
     */
    public function getRandomId()
    {
        $characters = array_merge(
            range(0, 9), range('a', 'z'),
            range('A', 'Z'), ['-', '_']
        );

        $length = count($characters);

        $id = "";

        for ($i = 0; $i < self::ID_LENGTH; $i++) {
            $id .= $characters[random_int(0, $length - 1)];
        }

        return $id;
    }

    /**
     * 画像の投稿者
     * @return BelongsTo
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id', 'users');
    }

    /**
     * アクセサ - url
     * @return string
     */
    public function getUrlAttribute(): string
    {
        return Storage::cloud()->url($this->attributes['filename']);
    }
}
