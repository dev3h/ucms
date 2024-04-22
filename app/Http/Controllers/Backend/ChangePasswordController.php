<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use App\Models\User;
use Illuminate\Http\Request;

class ChangePasswordController extends Controller
{
    public function changePassword(ChangePasswordRequest $request)
    {
        try {
            $data = $request->validated();
            $current = $this->getCurrentUser();
            $user = User::whereId($current->id)->firstOrFail();
            $user->update([
                'password' => bcrypt($data['password'])
            ]);
            return $this->sendSuccessResponse(true, __('Password is changed'));
        } catch (\Exception $e) {
            return $this->sendErrorResponse(__('Something went wrong'), $e->getMessage());
        }
    }
}
