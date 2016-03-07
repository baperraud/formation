<?php

namespace App\Frontend\Modules\Device;

use \OCFram\BackController;

class DeviceController extends BackController {
	public function executeIndex() {
		$this->Page->addVar('title', 'Device utilisé');

		$device = '';

		$Detect = new \Mobile_Detect();

		if ($Detect->isTablet()) {
			$device = 'TABLET';
			if ($Detect->isiOS()) {
				$device .= ' (iOS)';
			}
			elseif($Detect->isAndroidOS()) {
				$device .= ' (Android)';
			}
		}
		elseif ($Detect->isMobile() && !$Detect->isTablet()){
			$device = 'PHONE';
			if ($Detect->isiOS()) {
				$device .= ' (iOS)';
			}
			elseif($Detect->isAndroidOS()) {
				$device .= ' (Android)';
			}
		}
		else {
			$device = 'NON MOBILE DEVICE';
		}

		$this->Page->addVar('device_name', $device);
	}
}