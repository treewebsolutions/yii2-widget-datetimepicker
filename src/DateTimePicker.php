<?php

namespace tws\widgets\datetimepicker;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\JsExpression;
use yii\web\View;
use yii\widgets\InputWidget;

/**
 * Class DateTimePicker
 *
 * @link http://eonasdan.github.io/bootstrap-datetimepicker/
 *
 * @author Tree Web Solutions <treewebsolutions.com@gmail.com>
 */
class DateTimePicker extends InputWidget
{
	const INPUT_ADDON_START = 'start';
	const INPUT_ADDON_END = 'end';

	/**
	 * @var bool|string The input addon
	 */
	public $inputAddon = self::INPUT_ADDON_START;

	/**
	 * @var string The input addon content
	 */
	public $inputAddonContent = '<span class="glyphicon glyphicon-calendar"></span>';

	/**
	 * @var string The linked DateTimePicker widget selector
	 */
	public $linkedTo;

	/**
	 * @var array The container options
	 */
	public $containerOptions = [];

	/**
	 * @var array The client (JS) options
	 */
	public $clientOptions = [];

	/**
	 * @var array The client (JS) events
	 */
	public $clientEvents = [];

	/**
	 * @var string The client (JS) selector
	 */
	private $_clientSelector;

	/**
	 * @var string The global widget JS hash variable
	 */
	private $_hashVar;

	/**
	 * @inheritdoc
	 * @throws \yii\base\InvalidConfigException
	 */
	public function init()
	{
		// Call the parent
		parent::init();
		// Set properties
		$this->setupProperties();
		// Register assets
		$this->registerAssets();
	}

	/**
	 * @inheritdoc
	 */
	public function run()
	{
		// Widget content
		$content = [];
		// Begin widget tag
		$content[] = Html::beginTag('div', $this->containerOptions);
		// Render input HTML tag
		$content[] = $this->renderInputHtml('text');
		// Close the widget HTML tag
		$content[] = Html::endTag('div');
		// Render the input addon at the proper position
		if ($this->inputAddon) {
			if ($this->inputAddon === self::INPUT_ADDON_START) {
				array_splice($content, 1, 0, $this->renderInputAddon());
			} else {
				array_splice($content, 2, 0, $this->renderInputAddon());
			}
		}
		// Render the widget content
		return implode("\n", $content);
	}

	/**
	 * Gets the client selector.
	 *
	 * @return string
	 */
	public function getClientSelector()
	{
		if (!$this->_clientSelector) {
			$this->_clientSelector = '#' . $this->getId();
		}
		return $this->_clientSelector;
	}

	/**
	 * Gets the hash variable.
	 *
	 * @return string
	 */
	public function getHashVar()
	{
		if (!$this->_hashVar) {
			$this->_hashVar = 'datetimepicker_' . hash('crc32', $this->buildClientOptions());
		}
		return $this->_hashVar;
	}

	/**
	 * Sets the widget properties.
	 */
	protected function setupProperties()
	{
		// Ensure that input id is null if does not have a model attached
		if (!$this->hasModel()) {
			$this->options['id'] = null;
		}
		// Merge input options
		$this->options = ArrayHelper::merge([
			'class' => 'form-control',
			'autocomplete' => 'off',
			'data' => [
				'datetimepicker-options' => $this->getHashVar(),
			],
		], $this->options);
		// Ensure that containerOptions array contains an id key
		$this->containerOptions['id'] = $this->containerOptions['id'] ?: $this->getId();
		// Ensure default CSS class for the widget container
		Html::addCssClass($this->containerOptions, 'datetimepicker');
		// Ensure default CSS class for the input control
		Html::addCssClass($this->options, 'datetimepicker-input');
		// Check if the inputAddon is set
		if ($this->inputAddon) {
			// Add the proper CSS class
			Html::addCssClass($this->containerOptions, 'input-group');
		}
	}

	/**
	 * Builds the client options.
	 *
	 * @return string
	 */
	protected function buildClientOptions()
	{
		// Ensure default values
		$defaultClientOptions = [
			'format' => 'YYYY-MM-DD HH:mm:ss',
			'locale' => substr(Yii::$app->language, 0, 2),
		];
		// Merge client options
		$clientOptions = ArrayHelper::merge($defaultClientOptions, $this->clientOptions);
		// Convert date formats
		if (strncmp($clientOptions['format'], 'icu:', 4) === 0) {
			$clientOptions['format'] = MomentFormat::convertDateIcuToMoment(substr($clientOptions['format'], 4));
		} elseif (strncmp($clientOptions['format'], 'php:', 4) === 0) {
			$clientOptions['format'] = MomentFormat::convertDatePhpToMoment(substr($clientOptions['format'], 4));
		}
		// Return options as JSON
		return Json::encode($clientOptions);
	}

	/**
	 * Registers the widget assets.
	 */
	protected function registerAssets()
	{
		// Get the view
		$view = $this->getView();
		// Register assets
		DateTimePickerAsset::register($view);
		// Register widget hash JavaScript variable
		$view->registerJs("var {$this->getHashVar()} = {$this->buildClientOptions()};", View::POS_HEAD);
		// Build client script
		$js = "jQuery('{$this->getClientSelector()}').datetimepicker({$this->getHashVar()})";
		// Build client events
		if (!empty($this->clientEvents)) {
			foreach ($this->clientEvents as $clientEvent => $eventHandler) {
				if (!($eventHandler instanceof JsExpression)) {
					$eventHandler = new JsExpression($eventHandler);
				}
				$js .= ".on('{$clientEvent}', {$eventHandler})";
			}
		}
		// Register widget JavaScript
		$view->registerJs("{$js};");
		// Check if this widget is linked to another DateTimePicker
		if (!empty($this->linkedTo)) {
			// Register custom JS event to set this widget minDate client option
			$view->registerJs("jQuery('{$this->linkedTo}').on('dp.change', function (e) {
				var dtp = jQuery('{$this->getClientSelector()}').data('DateTimePicker').minDate(e.date);
				if (dtp.date() && e.date && e.date.isAfter(dtp.date())) {
					dtp.date(e.date);
				}
			});");
		}
	}

	/**
	 * Renders a Bootstrap input group addon.
	 *
	 * @return string
	 */
	protected function renderInputAddon()
	{
		return Html::tag('span', $this->inputAddonContent, [
			'class' => 'input-group-addon',
		]);
	}
}
