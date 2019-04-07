<?php
/**
 * MageSpecialist
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@magespecialist.it so we can send you a copy immediately.
 *
 * @category   MSP
 * @package    MSP_ReCaptcha
 * @copyright  Copyright (c) 2017 Skeeller srl (http://www.magespecialist.it)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace MSP\ReCaptcha\Plugin\Block\Account;

use Magento\Customer\Block\Account\AuthenticationPopup;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Json\DecoderInterface;
use Magento\Framework\Json\EncoderInterface;
use MSP\ReCaptcha\Model\Config;
use MSP\ReCaptcha\Model\LayoutSettings;

class AuthenticationPopupPlugin
{
    /**
     * @var EncoderInterface
     */
    private $encoder;

    /**
     * @var DecoderInterface
     */
    private $decoder;

    /**
     * @var LayoutSettings
     */
    private $layoutSettings;

    /**
     * @var Config
     */
    private $config;

    /**
     * AuthenticationPopupPlugin constructor.
     *
     * @param EncoderInterface $encoder
     * @param DecoderInterface $decoder
     * @param LayoutSettings $layoutSettings
     * @param Config|null $config
     */
    public function __construct(
        EncoderInterface $encoder,
        DecoderInterface $decoder,
        LayoutSettings $layoutSettings,
        Config $config = null
    ) {
        $this->encoder = $encoder;
        $this->decoder = $decoder;
        $this->layoutSettings = $layoutSettings;
        $this->config = $config ?: ObjectManager::getInstance()->get(Config::class);
    }

    /**
     * @param AuthenticationPopup $subject
     * @param array $result
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetJsLayout(AuthenticationPopup $subject, $result)
    {
        $layout = $this->decoder->decode($result);

        if ($this->config->isEnabledFrontend()) {
            $layout['components']['authenticationPopup']['children']['msp_recaptcha']['settings']
                = $this->layoutSettings->getCaptchaSettings();
        }

        if(
            !$this->config->isEnabledFrontend()
            && isset($layout['components']['authenticationPopup']['children']['msp_recaptcha'])
        ) {
            unset($layout['components']['authenticationPopup']['children']['msp_recaptcha']);
        }

        return $this->encoder->encode($layout);
    }
}
