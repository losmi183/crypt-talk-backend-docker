<?php

namespace App\Repository;

use App\Models\User;
use App\Models\Connection;
use App\Services\JWTServices;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;

class UserRepository
{
    private JWTServices $jwtServices;

    public function __construct(JWTServices $jwtServices) {
        $this->jwtServices = $jwtServices;
    }

    /**
     * @param array $data
     * 
     * @return User
     */
    public function store(array $data): User
    {
        try {
            $user = User::create($data);
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            abort(400, 'User not registered');
        }
        return $user;
    }

    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function search(array $data): Collection
    {
        $user = $this->jwtServices->getContent();
        $user_id = $user['id'];
        $search = $data['search'];

        return DB::table('users as u')
            ->leftJoin('user_connections as c', function ($join) use ($user_id) {
                $join->on(function ($query) use ($user_id) {
                    $query->where(function ($q) use ($user_id) {
                        $q->on('c.initiator_id', '=', 'u.id')
                        ->where('c.recipient_id', '=', $user_id);
                    })
                    ->orWhere(function ($q) use ($user_id) {
                        $q->on('c.recipient_id', '=', 'u.id')
                        ->where('c.initiator_id', '=', $user_id);
                    });
                });
            })
            ->where('u.id', '!=', $user_id)
            ->when($search ?? null, fn($q) => $q->where('u.name', 'LIKE', "%$search%"))
            ->select(
                'u.id as user_id',
                'u.name',
                'u.email',
                'c.id as connection_id',
                'c.accepted_at',
                DB::raw("
                    CASE
                        WHEN c.id IS NULL THEN NULL
                        WHEN c.accepted_at IS NOT NULL THEN 'FRIEND'
                        WHEN c.accepted_at IS NULL THEN 'PENDING'
                    END AS status
                ")
            )
            ->get();

        // return DB::table('users as u')
        //     ->leftJoin('user_connections as c', function ($join) {
        //         $join->on('c.initiator_id', '=', 'u.id')
        //             ->orOn('c.recipient_id', '=', 'u.id');
        //     })
        //     ->where('u.name', 'LIKE', '%'.$search.'%') // ako budeš hteo da dodaš pretragu
        //     ->where('u.id', '!=', $user_id)
        //     ->select(
        //         'u.id as user_id',
        //         'u.name',
        //         'u.email',
        //         'c.id as connection_id',
        //         'c.accepted_at',
        //         DB::raw("
        //             CASE
        //                 WHEN c.id IS NULL THEN NULL
        //                 WHEN c.accepted_at IS NOT NULL THEN 'FRIEND'
        //                 WHEN c.accepted_at IS NULL THEN 'PENDING'
        //             END AS status
        //         ")
        //     )
        //     ->get();


        
        // return DB::table('users as u')
        //     ->leftJoin('user_connections as c', function($join) use ($user_id) {
        //         $join->where(function($q) use ($user_id) {
        //             $q->where(function($subq) use ($user_id) {
        //                 $subq->where('c.initiator_id', $user_id)
        //                     ->where('c.recipient_id', 'u.id');
        //             })
        //             ->orWhere(function($subq) use ($user_id) {
        //                 $subq->where('c.recipient_id', $user_id)
        //                     ->where('c.initiator_id', 'u.id');
        //             });
        //         });
        //     })
        //     ->where(function($query) use ($search) {
        //         $query->where('u.email', 'LIKE', '%'.$search.'%')
        //             ->orWhere('u.name', 'LIKE', '%'.$search.'%');
        //     })
        //     ->where('u.id', '!=', $user_id)
        //     ->whereNull('c.accepted_at') // Samo oni bez prihvaćene konekcije
        //     ->select(
        //         'u.id as user_id', 
        //         'u.name', 
        //         'u.email',
        //         'c.id as connection_id'
        //     )
        //     ->get();
    }

    public function show(int $id): ?User
    {
        return User::find($id);
    }
}