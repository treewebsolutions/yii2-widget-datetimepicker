<?php

namespace tws\widgets\datetimepicker;

use Yii;
use yii\web\AssetBundle;

class MomentAsset extends AssetBundle
{
	/**
	 * @inheritdoc
	 */
	public $sourcePath = '@npm/moment';

	/**
	 * @inheritdoc
	 */
	public $js = [
		'min/moment-with-locales.min.js',
	];

	/**
	 * @inheritdoc
	 */
	public $depends = [
		'yii\web\JqueryAsset',
	];
}
