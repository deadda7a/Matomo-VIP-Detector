<?php
namespace Piwik\Plugins\VipDetector;

use Piwik\Common;
use Piwik\Plugins\Live\VisitorDetailsAbstract;
use Piwik\View;
use Piwik\Plugins\VipDetector\Dao\DatabaseMethods;

class VisitorDetails extends VisitorDetailsAbstract {
    // We extend the visitor details instead of doing this in the renderer, maybe the users want to do something else with this information
    /**
     * @throws \Exception
     */
    public function extendVisitorDetails(&$visitor) {
        $name = DatabaseMethods::getNameFromIp($visitor['visitIp']);
        $visitor['vipname'] = Common::sanitizeInputValues($name);
    }

    public function renderVisitorDetails($visitorDetails) {
        // Render the template
        $view = new View('@VipDetector/vip');
        $view->vipName = $visitorDetails['vipname'];
        $view->vipUrl = $this->getVipUrl($visitorDetails['vipname']);

        return [[30, $view->render()]];
    }

    // at the moment this always returns just the link to a DuckDuckGo search, but maybe we want the link in the database later
    private function getVipUrl(string $vip) {
        return sprintf("https://duckduckgo.com/?q=%s", urlencode($vip));
    }
}