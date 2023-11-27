<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
class PageNotFoundControllerCore extends FrontController
{
    public $php_self = 'pagenotfound';
    public $page_name = 'pagenotfound';
    public $ssl = true;

    public function parseDamageLinks(){
        $protocol = 'http';
        if(isset($_SERVER['HTTPS'])) {
            $protocol = 'https';
        }

        $base = $protocol . '://' . $_SERVER['SERVER_NAME'];

        $currentUrl = $protocol . '://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
        $prefix = $base . $_SERVER['REQUEST_URI'];

        $currentNameUrl = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
        echo $currentNameUrl . "</br>";
        $count = substr_count($currentNameUrl, '/');

        /*
        echo "<h3>Debug</h3>";
        echo "<pre>";
        echo "Slash: " .  $count . "</br>";
        echo "CurrentUrl: " . $currentUrl;
        echo "</br>";
        echo "Prefix: " . $prefix;
        echo "</br>";
        */

        if($count > 2 || $count <= 1){
            return "above-slash";
        }

        $urlParts = explode('/', $currentUrl);
        $desiredUrlParts = array_slice($urlParts, 3);
        $desiredUrl = implode('/', $desiredUrlParts);

        $startPos = strpos($desiredUrl, '/');
        $desiredUrl = substr($desiredUrl, $startPos + 1);

        /*
        echo "Path: " . $desiredUrl . "</br>";
        echo "</pre>";
        */

        $position = 0;
        if (strpos($currentUrl, $prefix) === 0) {
            if (preg_match('/\d+-.*/', substr($desiredUrl, $position))) {

                preg_match('/\d+/', $desiredUrl, $matches);
                $number = $matches[0];
                $newUrl = "product&id_product=" . $number;
                //echo $newUrl . "</br>";
                return $newUrl;
            } else {
                echo "no-number";
            }
        } else {
            echo "no-redirect";
        }
    }

    /**
     * Assign template vars related to page content.
     *
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        //echo "PageNotFoundControllerCore !!!!" . "</br>";
        $url = $this->parseDamageLinks();
        if($url != "above-slash" && $url != "no-number" && $url != "no-redirect"){
            Tools::redirect($url);
            die();
        }else{
            ob_clean();
            header('HTTP/1.1 404 Not Found');
            header('Status: 404 Not Found');
            $this->context->cookie->disallowWriting();
            parent::initContent();
            $this->setTemplate('errors/404');
        }
    }

    protected function canonicalRedirection($canonical_url = '')
    {
        // 404 - no need to redirect to the canonical url
    }

    protected function sslRedirection()
    {
        // 404 - no need to redirect
    }

    public function getTemplateVarPage()
    {
        $page = parent::getTemplateVarPage();
        $page['title'] = $this->trans('The page you are looking for was not found.', [], 'Shop.Theme.Global');

        return $page;
    }

    public function displayAjax()
    {
        header('Content-Type: application/json');
        echo json_encode($this->trans('The page you are looking for was not found.', [], 'Shop.Theme.Global'));
    }
}
