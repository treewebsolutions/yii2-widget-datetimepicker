<?php

namespace tws\widgets\datetimepicker;

use yii\web\AssetBundle;

class DateTimePickerAsset extends AssetBundle
{
	/**
	 * @inheritdoc
	 */
	public $sourcePath = '@npm/eonasdan-bootstrap-datetimepicker/build';

	/**
	 * @inheritdoc
	 */
	public $css = [
		'css/bootstrap-datetimepicker.min.css',
	];

	/**
	 * @inheritdoc
	 */
	public $js = [
		'js/bootstrap-datetimepicker.min.js',
	];

	/**
	 * @inheritdoc
	 */
	public $depends = [
		'yii\web\JqueryAsset',
		'yii\bootstrap\BootstrapAsset',
		'tws\widgets\datetimepicker\MomentAsset',
	];
}
