<?php

namespace App;

use App\Enums\AppConstants;
use App\Enums\UserTypeValues;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Collection;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * Class User
 * @package App
 * @property int    $id
 * @property int    $employee_id
 * @property int    $type
 * @property string $name
 * @property string $email
 */
class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     * @var array
     */
    protected $fillable = [
        'name', 'employee_id', 'type', 'email', 'password'
    ];

    /**
     * The attributes that should be hidden for arrays.
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     * @var array
     */
    protected $casts = [
        'email'             => 'string',
        'email_verified_at' => 'datetime',
        'employee_id'       => 'int',
        'name'              => 'string',
        'type'              => 'int',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [
            'id'          => $this->getKey(),
            'name'        => $this->name,
            'type'        => $this->type,
            'employee_id' => $this->employee_id,
            'email'       => $this->email
        ];
    }

    public function isAdmin(): bool
    {
        return $this->exists && $this->type === UserTypeValues::ADMIN;
    }

    /**
     * @param array $employeeIds
     *
     * @return Collection
     */
    public static function fetchByEmployeeIds(array $employeeIds): Collection
    {
        $localUserCollection = new Collection();
        if (!empty($employeeIds)) {
            foreach (array_chunk(array_unique($employeeIds), AppConstants::MYSQL_WHERE_IN_QUERY_LIMIT) as $chunk) {
                $users               = static::whereIn('employee_id', $chunk)->get();
                $localUserCollection = $localUserCollection->concat($users);
            }
        }

        return $localUserCollection->keyBy('employee_id');
    }
}
