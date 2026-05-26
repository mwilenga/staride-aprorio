<?php

namespace App\Http\Controllers\Integrations;

use App\Http\Controllers\Controller;
use App\Traits\DriverTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;


class LatraController extends Controller
{
    //
    use DriverTrait;

    protected $payload;

    public function __construct($data)
    {
        // dd($data);
        $token = $this->generate_self_signed_jwt();
       
        $this->payload = $data;
        $calling_for = $this->payload['calling_for'];
        $config = $this->payload['config'];

        $headers = [
            'Content-Type: application/json',
            'client-id: '.$config->api_key,
            'app-id: '.$config->auth_token,
            'app-key: '.$config->api_secret
        ];


        switch ($calling_for){
            case 'DRIVER_VEHICLE_REGISTRATION':
                $this->vehicle_registration($token, $data);
                break;
            case "DRIVER_REGISTRATION":
                
                $this->driver_registration($token, $data);
                break;
            case "TRIP_SUBMIT":
                // $this->trip_submit($token, $data);
                break;
        }

    }
    
    
    public  function generate_self_signed_jwt()
    {
        $privateKey = file_get_contents(storage_path('oauth-private.key'));

  $payload = [
            "iss" => 'https://dach.adminkloud.com/public/api', // client id provided by API provider
            "sub" => 'https://dach.adminkloud.com/public/api',
            "iat" => time(),               // issued at
            "nbf" => time(),               // not before
            "exp" => time() + 3600,        // expiry (1 hour)
            "jti" => uniqid()              // unique token id
        ];


        $jwt = JWT::encode($payload, $privateKey, 'RS256');

        return $jwt;
    }
    

    
    
    
    
    
 public function vehicle_registration($token, $data)
    {
        $url = "http://41.59.57.164:8080/api/v1/ticket-engine/rideVehicle/submitVehicle";
   
        $payload = [
            "vehicleRegistrationNumber" => $data['request']['vehicle_number'],
            "vehicleModel" => $data['request']['vehicle_model_id'],
            "vehicleChasisNumber" => $data['request']['body_number'],
            "vehicleTypeEnum" => $data['request']['vehicle_type_id'] ?? 'CAR',
            "vehicleRegistrationDate" => $data['request']['vehicle_registration_date']
        ];
      

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "accept: */*",
            "Authorization: Bearer " . $token,
            "Content-Type: application/json"
        ]);

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            return [
                'success' => false,
                'error' => curl_error($ch)
            ];
        }

        curl_close($ch);
        $res = json_decode($response, true);
        return $res;
       
    }
    
    
    public function driver_registration($token, $data){
   
   $url = "http://41.59.57.164:8080/api/v1/ticket-engine/rideDriver/submitDriver";

    $payload = json_encode([
        "driverName" => $data['driver']['first_name'] ?? 'test',
        "driverEmail" => $data['merchant']['email'] ?? 'test@gmail.com',
        "driverPhone" => $data['driver']['phoneNumber'] ?? '9898989898',
        "driverLicenseNumber" => $data['request']['document_number'] ?? '123456789',
        "licenceCategory" => $data['licenceCategory'] ?? 'L',
    ]);

    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'accept: */*',
        'Authorization: Bearer ' . $token,
        'Content-Type: application/json',
    ]);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        $error = curl_error($ch);
        curl_close($ch);
        return ['error' => $error];
    }
    $res = json_decode($response, true);

    curl_close($ch);
    //  \Log::channel('latra')->info('response',['data'=>$res]);

    return $res;
        
    }
public function trip_submit($token, $tripData)
{
    $url = "http://41.59.57.164:8080/api/v1/ticket-engine/rideHailing/submitTrip";

    $payload = [
        "tripId" => $tripData['tripId'],
        "originCoordinates" => $tripData['originCoordinates'],
        "endCoordinates" => $tripData['endCoordinates'],
        "startTime" => $tripData['startTime'],
        "endTime" => $tripData['endTime'],
        "totalFareAmount" => $tripData['totalFareAmount'],
        "tripDistance" => $tripData['tripDistance'],
        "rating" => $tripData['rating'],
        "driverEarning" => $tripData['driverEarning'],
        "driverLicenseNumber" => $tripData['driverLicenseNumber'],
        "vehicleRegistrationNumber" => $tripData['vehicleRegistrationNumber'],
        "passengerName" => $tripData['passengerName'],
        "passengerPhone" => $tripData['passengerPhone'],
        "passengerNida" => $tripData['passengerNida'] ?? null,
        "pickupPoint" => $tripData['pickupPoint'],
        "dropOffPoint" => $tripData['dropOffPoint'],
    ];

    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Accept: */*",
        "Authorization: Bearer " . $token,
        "Content-Type: application/json"
    ]);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        return [
            'success' => false,
            'error' => curl_error($ch)
        ];
    }

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    return [
        'success' => $httpCode === 200,
        'status_code' => $httpCode,
        'response' => json_decode($response, true)
    ];
}
  
}
