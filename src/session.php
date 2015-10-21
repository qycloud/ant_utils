<?php
namespace Utils;

final class Session extends Singleton
{

    /**
     * www 跨域上传文件到 fileio 时，
     * 因为 flash 发出的 http 请求不会携带浏览器存储的 cookie，
     * fileio 所在的机器会认为用户没有登录。
     *
     * 因此，允许主动把 session id 放在 GET/POST 里，以验证身份。
     *
     * 用户登录时会无条件重设 session id，不会导致 session fixation 的安全问题。
     */
    public function init()
    {
        if (!empty($_REQUEST['authToken']) && !empty($_REQUEST['authKey'])) {
            if (\Controller\Api2\Authorize\apiaccess::isHaveRecord(
                $_REQUEST['authToken'], $_REQUEST['authKey']
            ) === true) {
                session_id($_REQUEST['authToken']);
            } else {
                session_start();
                \Model\User\Login::instance()->signOut();
            }
        }

        if (!empty($_REQUEST['PHPSESSID'])) {
            session_id($_REQUEST['PHPSESSID']);
        }
        session_start();
    }

    public function get($key = null)
    {
        if ($key === null) {
            return $_SESSION;
        }

        return (array_key_exists($key, $_SESSION) ? $_SESSION[$key] : null);
    }

    public function set($key, $value)
    {
        $_SESSION[$key] = $value;
        return $this;
    }
}