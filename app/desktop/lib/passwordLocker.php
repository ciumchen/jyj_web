<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 *
 *
 */
class desktop_passwordLocker
{

    var $accountIdMap = [];


    /**
     * create a new scene instance
     *
     * @param  string  $scene
     * @return null
     */
    public function __construct()
    {
    }

    public function getUserIdByAccount($account)
    {
        if(isset($this->accountIdMap[$account]))
            return $this->accountIdMap[$account];
        $user = app::get('desktop')->model('account')->getRow('*', ['login_name'=>$account]);
        $this->$this->accountIdMap[$account] = $user['account_id'];
        return $user['account_id'];
    }

    /**
     * 获取小时ttl
     *
     * @return int
     */
    public function getHourTtl()
    {
        return 10;
    }

    /**
     * 获取分钟ttl
     *
     * @return int
     */
    public function getMinuteTtl()
    {
        return $this->getHourTtl() * 60;
    }

    /**
     * 获取重试次数
     *
     * @return int
     */
    public function getRetryTimes()
    {
        return 10;
    }

    /**
     * 获取需要提示的重试次数
     *
     * @return int
     */
    public function getRemindRetryTimes()
    {
        return 10;
    }

    /**
     * 生成对应的cache key
     *
     * @return string
     */
    protected function prepareKey($userId)
    {
        return 'password-lock_desktop_' . $userId;
    }

    /**
     * 检查是否已经锁定
     *
     * @return null
     */
    public function checkLock($userId)
    {
        if (cache::store('sysuser')->get($this->prepareKey($userId)) === 0) {
            throw new \LogicException(app::get('desktop')->_("密码错误{$this->getRetryTimes()}次，您可以联系系统管理员清楚缓存，或{$this->getHourTtl()}小时后再试。"));
        }
    }

    /**
     * 尝试重试
     *
     * @return null
     */
    public function tryVerify($userId)
    {
        $retryTimes = $this->getRetryTimes();
        $remindRetryTimes = $this->getRemindRetryTimes();
        $residualRetryTimes = cache::store('sysuser')->decrement($this->prepareKey($userId),
                                                                 1,
                                                                 $this->getRetryTimes(),
                                                                 $this->getMinuteTtl());

        $this->checkLock($userId);

        if ($residualRetryTimes <= $remindRetryTimes) {
            throw new \LogicException(app::get('desktop')->_("密码错误，您还可以尝试{$residualRetryTimes}次"));
        }
    }

    /**
     * 清除锁
     *
     * @return null
     */
    public function clean($userId) {
        cache::store('sysuser')->forget($this->prepareKey($userId));
    }
}

