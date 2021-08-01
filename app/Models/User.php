<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Laravel\Lumen\Auth\Authorizable;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable, HasFactory;

    const JWT_HEADER = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    protected $visible = [
        'name', 'id',
    ];

    static $listed = [
        'id', 'name',
    ];

    public function setPasswordAttribute($pass)
    {
        $this->attributes['password'] = Hash::make($pass);
    }

    public function scopeFilter($query, $params)
    {
        if (isset($params['name']) && trim($params['name'] !== ''))
            $query->where('name', 'LIKE', trim($params['name']) . '%');

        return $query;
    }

    public function generateJWT()
    {
        $header = User::JWT_HEADER;
        $now = time();

        $this->makeVisible('iat')->attributes['iat'] = $now;
        $this->makeVisible('exp')->attributes['exp'] = $now + env('JWT_LIFE');

        $payload = User::base64url_encode($this->toJson());

        $data = "$header.$payload";
        $sign = $this->signature($data);

        return ['token' => "$data.$sign"];
    }

    public static function validateJWT(string $token)
    {
        $arr = explode('.', $token);
        if (count($arr) == 3) {
            $now = time();

            $sign = array_pop($arr);
            $data = implode('.', $arr);

            $payload = json_decode(User::base64url_decode($arr[1]));
            if (!$payload)
                $payload = (object)['id' => '', 'iat' => '', 'exp' => ''];

            $valid_hdr = $arr[0] == User::JWT_HEADER;
            $valid_sign = User::signature($data) == $sign;
            $valid_time = $payload->iat <= $now && $payload->exp >= $now;

            if ($valid_hdr && $valid_sign && $valid_time)
                return User::find($payload->id);
        }
    }

    public static function signature($data)
    {
        $secret = env('JWT_SECRET');
        return User::base64url_encode(hash_hmac('sha256', $data, $secret, true));
    }

    public static function base64url_encode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    public static function base64url_decode($data)
    {
        return base64_decode(strtr($data, '-_', '+/') . str_repeat('=', 3 - (3 + strlen($data)) % 4));
    }
}
