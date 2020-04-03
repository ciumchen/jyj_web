<?php
/**
 * @copyright  Copyright (c) 2005-2016 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class topmanageapi_token_default implements topmanageapi_interface_token {

    public $expire = '2592000';//60*60*24*30

    public function make($userId, $data)
    {
        $redis = redis::scene('topmanageapi_token');

        $systemToken = base_certi::token();
        $userKey = md5($systemToken.$userId);

        $randomId = str_random(32);
        // $token = md5(sha1(
        //     json_encode(
        //         array(
        //             $userId,
        //             $data['account'],
        //             $data['password'],
        //             $data['deviceid'],
        //             $randomId,
        //         )
        //     )
        // )).$userKey;

        $token = md5(sha1($userId.implode('', $data).$randomId)).$userKey;

        $value = json_encode(['user_id'=>$userId, 'oAuthInfo'=>$data, 'deviceid'=>$data['deviceid'], 'expire'=>time()+$this->expire]);
        $redis->hset($userKey, $token, $value);

        return $token;
    }

    public  function check($token)
    {
        $redis = redis::scene('topmanageapi_token');

        $userKey = substr($token, 32, 64);
        if( !$userKey )
        {
            throw new \RuntimeException('invalid token', 2000101);
        }

        $userData = $redis->hget($userKey, $token);
        if( ! $userData )
        {
            throw new \RuntimeException('invalid token', 2000102);
        }

        $data = json_decode($userData, true);
        if( $data['expire'] < time()  )
        {
            $redis->hdel($userKey, $token);
            throw new \RuntimeException('invalid token', 2000103);
        }
        else
        {
            $value = json_encode(['user_id'=>$data['user_id'], 'oAuthInfo'=>$data['oAuthInfo'], 'deviceid'=>$data['deviceid'], 'expire'=>time()+$this->expire]);
            $redis->hset($userKey, $token, $value);
        }

        return $data['oAuthInfo'];
    }

    public function delete($token)
    {
        $redis = redis::scene('topmanageapi_token');

        $userKey = substr($token, 32, 64);
        return $redis->hdel($userKey, $token);
    }

    public function deleteUser($userId)
    {
        $redis = redis::scene('topmanageapi_token');

        $systemToken = base_certi::token();
        $userKey = md5($systemToken.$userId);

        $keys = $redis->HKEYS($userKey);
        foreach( $keys as $key )
        {
            $redis->HDEL($userKey, $key);
        }

        return true;
    }
}

