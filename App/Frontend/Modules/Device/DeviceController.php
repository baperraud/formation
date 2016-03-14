<?php

namespace App\Frontend\Modules\Device;

use OCFram\BackController;

class DeviceController extends BackController {
	public function executeIndex() {
		$this->Page->addVar('title', 'Device utilisé');

		$Detect = new \Mobile_Detect();

		if ($Detect->isTablet()) {
			$device = 'TABLET';
			/** @noinspection PhpUndefinedMethodInspection */
			if ($Detect->isiOS()) {
				$device .= ' (iOS)';
			} /** @noinspection PhpUndefinedMethodInspection */ elseif($Detect->isAndroidOS()) {
				$device .= ' (Android)';
			}
		}
		elseif ($Detect->isMobile() && !$Detect->isTablet()){
			$device = 'PHONE';
			/** @noinspection PhpUndefinedMethodInspection */
			if ($Detect->isiOS()) {
				$device .= ' (iOS)';
			} /** @noinspection PhpUndefinedMethodInspection */ elseif($Detect->isAndroidOS()) {
				$device .= ' (Android)';
			}
		}
		else {
			$device = 'NON MOBILE DEVICE';
		}

		$this->Page->addVar('device_name', $device);
	}
}