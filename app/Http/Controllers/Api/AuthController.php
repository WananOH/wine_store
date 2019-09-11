<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use EasyWeChat\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AuthController extends Controller{

    public function login(Request $request)
    {
        $app = Factory::officialAccount(config('wechat.official_account.default'));
        $oauth = $app->oauth;
        $user = $oauth->user()->getOriginal();
        $data = [
            'name' => $user['nickname'],
            'nickname' => $user['nickname'],
            'avatar' => $user['headimgurl'],
            'parent_id' => $request->parent_id ?? 0
        ];

        $user = User::updateOrCreate(['openid' => $user['openid']], $data);;
        if (\Auth::loginUsingId($user->id)) {
            $user = \Auth::user();
//            if(!$user->qrcode){
//                $user->qrcode = $this->generateQrcode($user->id);
//                $user->save;
//            }
            $token = $user->createToken($user->id . '-' . $user->openid)->accessToken;

            return response()->json(['status_code' => 201,'message' => '登录成功','data' => $token]);
        } else {
            return response()->json(['status_code' => 404,'message' => '用户不存在']);
        }
    }

    public function generateQrcode($id){
        QrCode::format('png')->size(120)->errorCorrection('H')->generate($id, storage_path('app/public/images/' . 'qrcode-'.$id . '.png'));

        return $qrcode_contents = Storage::disk('public')->url('/images/' .  'qrcode-'.$id . '.png');
    }


}
