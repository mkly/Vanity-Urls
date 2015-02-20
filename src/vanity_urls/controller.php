<?php
namespace Concrete\Package\VanityUrls;
defined('C5_EXECUTE') or die('Access Denied.');

use UserInfo;
use Events;
use Request;
use Package;
use Concrete\Core\Routing\Redirect;
use Concrete\Core\Routing\URL;

class Controller extends Package
{
    protected $pkgHandle = 'vanity_urls';
    protected $appVersionRequired = '5.7.1';
    protected $pkgVersion = '0.9.0';

    public function getPackageDescription()
    {
        return t('Allow members to have a @username url');
    }

    public function getPackageName()
    {
        return t('Vanity Urls');
    }

    public function on_start()
    {
        Events::addListener('on_start', array($this, 'handlePageView'));
    }

    public function handlePageView($event)
    {
        $path = Request::getInstance()->getPath();

        if (strpos($path, '/@') !== 0) {
            return;
        }

        $matches = array();
        preg_match('%/@([^/]+)+%', $path, $matches);
        if (count($matches) < 2) {
            return;
        }
        $userName = $matches[1];

        $user = UserInfo::getByUserName($userName);
        if (!$user) {
            return;
        }

        $url = URL::to('/members/profile', $user->getUserID());
        $r = Redirect::url($url, 301);
        $r->send();
        exit;
    }
}
