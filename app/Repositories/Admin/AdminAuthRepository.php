<?php
namespace App\Repositories\Admin;
use App\Models\Admin;
use Exception;
class AdminAuthRepository implements AdminRepositoryInterface
{
    public function create(array $data): Admin
    {
        try {
            $admin = new Admin($data);
            $admin->save();

            // Assign the role to the newly created admin
            if (isset($data['role'])) {
                $admin->assignRole($data['role']);
            }

            return $admin;
        } catch (Exception $e) {
            throw new Exception('Error creating admin: ' . $e->getMessage());
        }
    }

    public function findByEmail(string $email): ?Admin
    {
        return Admin::where('email', $email)->first();
    }
}
