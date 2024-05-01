<?php

namespace App\Traits;

use Twilio\Rest\Client;
use App\Models\RehabNotificationSetting;
use App\Models\Notification;
use App\Models\NotificationUser;
trait TraitController
{

	public function resp($status = 0, $message = '', $data = [], $code = 200)
	{
		return response()->json(array_merge(['status' => $status, 'message' => $message], $data), $code);
	}
	public function sendSMS($data)
	{
		try {
			$sid    = getenv('TWILIO_SID');
			$token  = getenv('TWILIO_AUTH_TOKEN');
			$number = getenv('TWILIO_NUMBER');
			$twilio = new Client($sid, $token);
			$message = $twilio->messages
				->create(
					$data['phone'], // to
					array(
						"messagingServiceSid" => getenv('TWILIO_SENDER_ID'),
						"body" => $data['message_text']
					)
				);
			return 1;
		} catch (\Throwable $th) {
			return $th;
		}
	}
	public function sendWhatsapp($data)
	{
		try {
			$sid    = getenv('TWILIO_SID');
			$token  = getenv('TWILIO_AUTH_TOKEN');
			$number = getenv('TWILIO_WHATSAPP_NUMBER');
			$twilio = new Client($sid, $token);
			$message = $twilio->messages
				->create(
					"whatsapp:" . $data['phone'], // to
					[
						"from" => "whatsapp:" . $number,
						"body" => $data['message_text'],
					]
				);
			return 1;
		} catch (\Throwable $th) {
			return $th;
		}
	}

	public function createNotification($notification)
	{
		$user = RehabNotificationSetting::select('users')->where('rehab_id', logRehabId())->first();
		if ($user) {
			$user = RehabNotificationSetting::getUsers($user->users);
			$notification['rehab_id'] = logRehabId();
			$notification['created_by'] = logId();
			$notification['status'] = Notification::UNREAD_STATUS;
			$notification = Notification::create($notification);
			$notificationUser = [];
			foreach ($user as $key => $value) {
				$notificationUser[$key]['user_id'] = $value;
				$notificationUser[$key]['notification_id'] = $notification->getId();
				$notificationUser[$key]['is_read'] = NotificationUser::UNREAD_STATUS;
				$notificationUser[$key]['created_at'] = dbDate(now());
				$notificationUser[$key]['updated_at'] = dbDate(now());
			}
			NotificationUser::insert($notificationUser);
		}
	}
}
