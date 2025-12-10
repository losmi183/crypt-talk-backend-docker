<?php

namespace App\Services;
use App\Models\User;
use App\Repository\UserRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;

class UserServices {

    private UserRepository $userRepository;
    private JWTServices $jwtServices;

    public function __construct(UserRepository $userRepository, JWTServices $jwtServices) {
        $this->userRepository = $userRepository;
        $this->jwtServices = $jwtServices;
    }

   
    public function search(array $data)
    {
        return $this->userRepository->search($data);
    }

    public function show(int $id): ?User
    {
        return $this->userRepository->show($id);
    }
    public function edit(): ?array
    {
        return $this->jwtServices->getContent();
    }

    public function update(array $data): User
    {
        $user = $this->jwtServices->getContent();
        $user_id = $user['id'];

        // 1. Provera da li postoji avatar fajl
        if (isset($data['avatar']) && $data['avatar'] instanceof \Illuminate\Http\UploadedFile && $data['avatar']->isValid()) {
            // napravi folder ako ne postoji
            $destination = public_path('images/avatar');
            if (!file_exists($destination)) {
                mkdir($destination, 0777, true);
            }

            $extension = $data['avatar']->getClientOriginalExtension();
            $filename = $user_id . '.' . $extension;

            // snimi u /public/images/avatar
            $data['avatar']->move($destination, $filename);

            // sačuvaj relativnu putanju (da lako praviš URL)
            $data['avatar'] = $filename;
        } else {
            unset($data['avatar']);
        }

        // update user-a
        // $data['avatar'] = $filename;
        try {
            $user = User::find($user_id);
            $user->update($data);
            $user->save();
        } catch (\Throwable $th) {
            //throw $th;
        }

        return $user;
    }

    public function forgotPassword(array $data): bool
    {
        // send email to $data['password']
        return true;
    }

 }