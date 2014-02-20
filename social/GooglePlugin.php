<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2013
 * @package yii2-social
 * @version 1.0.0
 */

namespace kartik\social;

use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\Html;

/**
 * Widget to render various Google plugins
 * 
 * Usage:
 * ```
 * echo GooglePlugin::widget([
 *     'type' => GooglePlugin::SHARE
 * ]);
 * ```
 * 
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class GooglePlugin extends Widget
{

    const SIGNIN = 'g-signin';
    const PLUS_ONE = 'g-plusone';
    const SHARE = 'g-plus';
    const BADGE_PAGE = 'g-page';
    const BADGE_PERSON = 'g-person';
    const BADGE_COMMUNITY = 'g-community';
    const FOLLOW = 'g-follow';
    const HANGOUT = 'g-hangout';
    const INTERACTIVE_POST = 'g-interactivepost';

    /**
     * @var string the Google plugin type
     * defaults to Google Plus One
     */
    public $type = self::PLUS_ONE;

    /**
     * @var string the Google Plus Client ID.
     */
    public $clientId;

    /**
     * @var string the Google Plus Profile ID.
     */
    public $profileId;

    /**
     * @var string the Google Page ID.
     */
    public $pageId;

    /**
     * @var array the HTML attributes for the signin container
     */
    public $signinOptions = [];

    /**
     * Initialize the widget
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();
        $this->setConfig('google');
        if (empty($this->type)) {
            throw new InvalidConfigException("The plugin 'type' must be set.");
        }
        if ($this->type === self::SIGNIN && empty($this->clientId)) {
            throw new InvalidConfigException("The Google 'clientId' must be set for the signin button.");
        }
        if ($this->type === self::FOLLOW && empty($this->pageId)) {
            throw new InvalidConfigException("The Google 'pageId' must be set for the follow button.");
        }
        if ($this->type === self::BADGE_PERSON && empty($this->profileId)) {
            throw new InvalidConfigException("The Google 'profileId' must be set for the person badge.");
        }
        if ($this->type === self::BADGE_PAGE && empty($this->pageId)) {
            throw new InvalidConfigException("The Google 'pageId' must be set for the page badge.");
        }
        if (!isset($this->noscript)) {
            $this->noscript = Yii::t('social', 'Please enable JavaScript on your browser to view the Google {pluginName} plugin correctly on this site.', ['pluginName' => Yii::t('social', str_replace('ga-', '', $this->type))]
            );
        }
        $this->registerAssets();
        $this->setPluginOptions();
        $content = Html::tag($this->tag, '', $this->options);
        if ($this->type === self::SIGNIN) {
            $content = Html::tag($this->tag, $content, $this->signinOptions);
        }
        echo $content . "\n" . $this->renderNoScript();
    }

    /**
     * Sets the options for the Google plugin
     */
    protected function setPluginOptions()
    {
        parent::setPluginOptions();
        if ($this->type === self::SIGNIN) {
            $this->options["data-clientid"] = $this->clientId;
        }
        elseif ($this->type === self::SHARE) {
            $this->options["data-action"] = 'share';
        }
        elseif ($this->type === self::BADGE_PERSON) {
            $this->options["data-href"] = "https://plus.google.com/{$this->profileId}";
        }
        elseif ($this->type === self::FOLLOW || $this->type === self::BADGE_PAGE) {
            $this->options["data-href"] = "https://plus.google.com/{$this->pageId}";
        }
    }

    /**
     * Registers the necessary assets
     */
    protected function registerAssets()
    {
        $view = $this->getView();
        $js = <<< SCRIPT
(function() {
    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
    po.src = 'https://apis.google.com/js/platform.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
  })();
SCRIPT;
        $view->registerJs($js);
    }

}