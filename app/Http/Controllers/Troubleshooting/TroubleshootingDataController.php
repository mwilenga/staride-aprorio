<?php

namespace App\Http\Controllers\Troubleshooting;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\CountryArea;
use App\Models\PriceCard;
use App\Models\PricingParameter;
use App\Models\VehicleMake;
use App\Models\VehicleModel;
use App\Models\VehicleType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Redis;
class TroubleshootingDataController extends Controller
{
    /**
     * Basic API-key check for external troubleshooting clients.
     * Still protects the endpoints, but merchant scoping is done
     * via merchant_id / user_id / driver_id parameters.
     */
    protected function assertAuthorized(Request $request): void
    {
        $configuredKey = env('TROUBLESHOOTING_API_KEY') ?: env('AI_API_KEY');

        if (!$configuredKey) {
            abort(503, 'Troubleshooting API key is not configured. Set TROUBLESHOOTING_API_KEY (or AI_API_KEY) in .env');
        }

        $providedKey = $request->header('X-Troubleshooting-API-Key')
            ?: $request->header('X-AI-API-Key');

        if (!$providedKey || !hash_equals($configuredKey, $providedKey)) {
            abort(401, 'Invalid or missing Troubleshooting API key');
        }
    }

    /**
     * Helper to fetch merchant_id from request (query or JSON/body).
     */
    protected function getMerchantIdFromRequest(Request $request): int
    {
        $merchantId = (int) $request->input('merchant_id');

        if ($merchantId <= 0) {
            abort(422, 'merchant_id is required and must be a positive integer');
        }

        return $merchantId;
    }

    /**
     * 1. Admin / Dashboard API
     * Returns a compact view of platform health for a single merchant.
     */
    public function adminDashboard(Request $request)
    {
        $this->assertAuthorized($request);
        $merchantId = $this->getMerchantIdFromRequest($request);
        $merchant = \App\Models\Merchant::find($merchantId);

        $now = Carbon::now();
        $soon = $now->copy()->addDays(14);

        $activeUsers = DB::table('users')
            ->where('merchant_id', $merchantId)
            ->whereNull('user_delete')
            ->count();

        $activeDrivers = DB::table('drivers')
            ->where('merchant_id', $merchantId)
            ->where('online_offline', 1)
            ->whereNull('driver_delete')
            ->count();

        // Best-effort dispute count (schema varies by installation)
        $openDisputes = 0;
        if (Schema::hasColumn('bookings', 'booking_issue_id') && Schema::hasColumn('bookings', 'merchant_id')) {
            $openDisputes = DB::table('bookings')
                ->where('merchant_id', $merchantId)
                ->whereNotNull('booking_issue_id')
                ->count();
        }
        
        $todayEarnings = 0;
        $weekEarnings = 0;
        $monthEarnings = 0;
        $allEarnings = 0;
        if (Schema::hasTable('booking_transactions') && Schema::hasColumn('booking_transactions', 'company_earning') && Schema::hasColumn('booking_transactions', 'merchant_id')) {
            $todayEarnings = DB::table('booking_transactions')
            ->where('merchant_id', $merchantId)
            ->whereDate('created_at', $now->toDateString())
            ->sum('company_earning');
    
            $weekEarnings = DB::table('booking_transactions')
                ->where('merchant_id', $merchantId)
                ->whereBetween('created_at', [$now->copy()->startOfWeek(), $now->copy()->endOfDay()])
                ->sum('company_earning');
        
            $monthEarnings = DB::table('booking_transactions')
                ->where('merchant_id', $merchantId)
                ->whereBetween('created_at', [$now->copy()->startOfMonth(), $now->copy()->endOfDay()])
                ->sum('company_earning');
                
            $allEarnings = DB::table('booking_transactions')
                ->where('merchant_id', $merchantId)
                ->sum('company_earning');
        }

        $serviceAreas = CountryArea::where('merchant_id', $merchantId)
            ->with('Country')
            ->whereHas('PriceCard', function ($query) use ($merchantId) {
                $query->where('merchant_id', $merchantId)
                    ->where('status', 1);
            })
            ->get()
            ->map(function ($area) {
                return [
                    'id'      => $area->id,
                    'name'    => $area->CountryAreaName,
                    'status'  => $area->status,
                    'country' => [
                        'id'   => $area->Country?->id,
                        'name' => $area->Country?->getCountryNameAttribute(),
                    ],
                ];
            });

        $documentsNearExpiry = [
            'drivers' => DB::table('driver_documents')
                ->join('drivers', 'drivers.id', '=', 'driver_documents.driver_id')
                ->join('documents', 'documents.id', '=', 'driver_documents.document_id')
                ->where('drivers.merchant_id', $merchantId)
                ->whereBetween('driver_documents.expire_date', [$now->toDateString(), $soon->toDateString()])
                ->select([
                    'driver_documents.id',
                    'driver_documents.driver_id',
                    'drivers.first_name',
                    'drivers.last_name',
                    'driver_documents.expire_date',
                    'driver_documents.document_verification_status',
                ])
                ->limit(50)
                ->get(),
            'users' => DB::table('user_documents')
                ->join('users', 'users.id', '=', 'user_documents.user_id')
                ->join('documents', 'documents.id', '=', 'user_documents.document_id')
                ->where('users.merchant_id', $merchantId)
                ->whereBetween('user_documents.expire_date', [$now->toDateString(), $soon->toDateString()])
                ->select([
                    'user_documents.id',
                    'user_documents.user_id',
                    'users.first_name',
                    'users.last_name',
                    'user_documents.expire_date',
                    'user_documents.document_verification_status',
                ])
                ->limit(50)
                ->get(),
        ];
        
        $idleDrivers = DB::table('drivers')
            ->where('merchant_id', $merchantId)
            ->where('online_offline', 1)  // online
            ->where('free_busy', 2)       // free
            ->where('login_logout', 1)    // logged in
            ->count();
        
        $busyDrivers = DB::table('drivers')
            ->where('merchant_id', $merchantId)
            ->where('online_offline', 1)  // online
            ->where('free_busy', 1)       // busy
            ->where('login_logout', 1)    // logged in
            ->count();
        
        $offlineDrivers = DB::table('drivers')
            ->where('merchant_id', $merchantId)
            ->where('online_offline', 2)  // offline
            ->count();
        
        $driverLocations = DB::table('drivers')
            ->where('merchant_id', $merchantId)
            ->where('online_offline', 1)
            ->where('login_logout', 1)
            ->select([
                'id as driver_id',
                DB::raw("CONCAT(first_name, ' ', last_name) AS driver_name"),
                'current_latitude',
                'current_longitude',
                'free_busy',
                'last_location_update_time'
            ])
            ->get();
        
        // Override lat/long from Redis if enabled
        if ($merchant->ApplicationConfiguration->working_with_redis == 1) {
            $driverLocations = $driverLocations->map(function ($driver) use ($merchantId) {
                $redisLocation = Redis::hgetall("driver_location:{$merchantId}:{$driver->driver_id}");
        
                if (
                    isset($redisLocation['latitude']) &&
                    isset($redisLocation['longitude']) &&
                    isset($redisLocation['timestamp'])
                ) {
                    $driver->current_latitude        = $redisLocation['latitude'];
                    $driver->current_longitude       = $redisLocation['longitude'];
                    $driver->last_location_update_time = $redisLocation['timestamp'];
                }
        
                return $driver;
            })
            ->filter(function ($driver) {
                // only return drivers who have valid location
                return !empty($driver->current_latitude) && !empty($driver->current_longitude);
            })
            ->values(); // re-index collection
        } else {
            // If not Redis, filter from DB columns
            $driverLocations = $driverLocations->filter(function ($driver) {
                return !empty($driver->current_latitude) && !empty($driver->current_longitude);
            })->values();
        }
        
        $today = now()->toDateString();

        return response()->json([
            'generated_at' => $now->toIso8601String(),
            'merchant_id'  => $merchantId,
            'summary'      => [
                'active_users'    => $activeUsers,
                'active_drivers'  => $activeDrivers,
                'open_disputes'   => $openDisputes,
                'today_earnings'  => number_format($todayEarnings,2),
                'week_earnings' => number_format($weekEarnings,2),
                'month_earning' => number_format($monthEarnings,2),
                'total_revenue' => number_format($allEarnings,2)
            ],
            'trips_data' => [
                'active_trips' => DB::table('bookings')
                    ->where('merchant_id', $merchantId)
                    ->whereIn('booking_status', [1000, 1001, 1002, 1003, 1004, 1012, 1019])
                    ->count(),
            
                'completed_trips_today' => DB::table('bookings')
                    ->where('merchant_id', $merchantId)
                    ->whereDate('created_at', $today)
                    ->where('booking_status', 1005)
                    ->count(),
            
                'cancelled_trips_today' => DB::table('bookings')
                    ->where('merchant_id', $merchantId)
                    ->whereDate('created_at', $today)
                    ->whereIn('booking_status', [1006, 1007, 1008, 1016, 1018])
                    ->count(),
            
                'average_trip_value' => DB::table('bookings')
                    ->where('merchant_id', $merchantId)
                    ->whereDate('created_at', $today)
                    ->where('booking_status', 1005)
                    ->avg('final_amount_paid') ?? 0,
            ],
            'service_areas'          => $serviceAreas,
            'documents_near_expiry'  => $documentsNearExpiry,
            'drivers_data' => [
                'idle_drivers'    => $idleDrivers,
                'busy_drivers'    => $busyDrivers,
                'offline_drivers' => $offlineDrivers,
                'driver_locations'=> $driverLocations,
            ],
        ]);
    }

    /**
     * 2. Configuration API
     * Returns pricing + vehicle configuration for a single merchant.
     */
    public function configuration(Request $request)
    {
        $this->assertAuthorized($request);
        $merchantId = $this->getMerchantIdFromRequest($request);

        $vehicleTypes = VehicleType::where('merchant_id', $merchantId)
            ->with(['LanguageVehicleTypeSingle', 'LanguageVehicleTypeAny'])
            ->get()
            ->map(function ($type) {
                return [
                    'id'          => $type->id,
                    'name'        => $type->vehicle_type_name,
                    'image'       => $type->vehicleTypeImage,
                    'status'      => $type->vehicleTypeStatus,
                ];
            });

        $vehicleMakes = VehicleMake::where('merchant_id', $merchantId)
            ->whereNull('admin_delete')
            ->with(['LanguageVehicleMakeSingle', 'LanguageVehicleMakeAny'])
            ->get()
            ->map(function ($make) {
                return [
                    'id'          => $make->id,
                    'name'        => $make->LanguageVehicleMakeSingle->vehicleMakeName
                        ?? $make->LanguageVehicleMakeAny->vehicleMakeName
                            ?? null,
                    'description' => $make->LanguageVehicleMakeSingle->vehicleMakeDescription
                        ?? $make->LanguageVehicleMakeAny->vehicleMakeDescription
                            ?? null,
                    'image'       => $make->vehicleMakeLogo,
                    'status'       => $make->vehicleMakeStatus,
                ];
            });

        $vehicleModels = VehicleModel::where('merchant_id', $merchantId)
            ->with(['LanguageVehicleModelSingle', 'LanguageVehicleModelAny', 'VehicleMake', 'VehicleType'])
            ->get()
            ->map(function ($model) {
                return [
                    'id'            => $model->id,
                    'name'          => $model->vehicle_model_name,
                    'description'   => $model->vehicle_model_description,
                    'vehicle_make'  => [
                        'id'    => $model->VehicleMake?->id,
                        'name'  => $model->VehicleMake?->LanguageVehicleMakeSingle->vehicleMakeName
                            ?? $model->VehicleMake?->LanguageVehicleMakeAny->vehicleMakeName
                                ?? null,
                    ],
                    'vehicle_type'  => [
                        'id'    => $model->VehicleType?->id,
                        'name'  => $model->VehicleType?->vehicle_type_name,
                    ],
                    'status'   => $model->vehicleModelStatus,
                ];
            });

        $priceCards = PriceCard::where('merchant_id', $merchantId)
        ->with([
            'VehicleType.LanguageVehicleTypeSingle',
            'VehicleType.LanguageVehicleTypeAny',
            'CountryArea',
            'Segment',
            'PriceCardValues.PricingParameter' // load parameter values with their parameter details
        ])
        ->get()
        ->map(function ($card) {
            return [
                'id'        => $card->id,
                'base_fare' => $card->base_fare,
                'free_time' => $card->free_time,
                'free_distance' => $card->free_distance,
                'base_fare_price_card_slab_id' => $card->base_fare_price_card_slab_id,
    
                'vehicle_type' => [
                    'id'   => $card->VehicleType?->id,
                    'name' => $card->VehicleType?->vehicle_type_name,
                ],
    
                'country_area' => [
                    'id'     => $card->CountryArea?->id,
                    'name'   => $card->CountryArea?->CountryAreaName,
                    'status' => $card->CountryArea?->status,
                ],
    
                'segment' => [
                    'id'   => $card->Segment?->id,
                    'name' => $card->Segment?->name,
                ],
    
                'parameters' => $card->PriceCardValues->map(function ($value) {
                    $parameter = $value->PricingParameter;
                    return [
                        'pricing_parameter_id' => $value->pricing_parameter_id,
                        'parameter_name'       => $parameter?->ParameterName,
                        'parameter_type'       => $parameter?->parameterType,
                        'sequence_number'      => $parameter?->sequence_number,
    
                        // core value (check_box_values)
                        'parameter_price'      => $value->parameter_price,
    
                        // free value (checkboxFreeArray) — used in parameterType 6, 9, 13, 18
                        'free_value'           => $value->free_value,
    
                        // value type (percentage/fixed) — used in parameterType 13
                        'value_type'           => $value->value_type,
    
                        // discount type — used in parameterType 11
                        'discount_value_type'  => $value->discount_value_type,
    
                        // additional fare type — used in parameterType 23
                        'additional_fare_value_type' => $value->additional_fare_value_type,
    
                        // ride later extra fare type — used in parameterType 24
                        'ride_later_extra_fare_value_type' => $value->ride_later_extra_fare_value_type,
    
                        // slab — used in parameterType 1, 8
                        'price_card_slab_id'   => $value->price_card_slab_id,
                    ];
                })->sortBy('sequence_number')->values(),
            ];
        });

        $pricingParameters = PricingParameter::where('merchant_id', $merchantId)
            ->with(['LanguageSingle', 'LanguageAny'])
            ->get()
            ->map(function ($param) {
                return [
                    'id'               => $param->id,
                    'name'             => $param->parameter_name,
                    'application_name' => $param->parameter_application,
                ];
            });

        $serviceAreas = CountryArea::where('merchant_id', $merchantId)
            ->with('Country')
            ->get()
            ->map(function ($area) {
                return [
                    'id'           => $area->id,
                    'name'         => $area->CountryAreaName,
                    'status'       => $area->status,
                    'country'      => [
                        'id'   => $area->Country?->id,
                        'name' => $area->Country->getCountryNameAttribute(),
                    ],
                ];
            });

        return response()->json([
            'generated_at'  => Carbon::now()->toIso8601String(),
            'merchant_id'   => $merchantId,
            'vehicles'      => [
                'types'  => $vehicleTypes,
                'makes'  => $vehicleMakes,
                'models' => $vehicleModels,
            ],
            'pricing'       => [
                'price_cards'        => $priceCards,
                'pricing_parameters' => $pricingParameters,
            ],
            'service_areas' => $serviceAreas,
        ]);
    }

    /**
     * 3. Driver/User Activity API
     * Returns latest trips and wallet / document status for a single entity.
     *
     * Required params (query or body):
     * - type = user|driver
     * - id   = user_id or driver_id
     */
    public function activity(Request $request)
    {
        $this->assertAuthorized($request);

        $type = $request->input('type', $request->query('type', 'user'));
        $id = (int) $request->input('id', $request->query('id'));

        if (!$id || !in_array($type, ['user', 'driver'], true)) {
            abort(422, 'Params "type" (user|driver) and numeric "id" are required');
        }

        if ($type === 'user') {
            return $this->userActivity($id);
        }

        return $this->driverActivity($id);
    }

    protected function userActivity(int $userId)
    {
        $user = \App\Models\User::where('id', $userId)->first();
        if (!$user) {
            abort(404, 'User not found');
        }

        $bookings = DB::table('bookings')
            ->where('user_id', $userId)
            ->orderBy('id', 'desc')
            ->limit(20)
            ->get(['id', 'booking_status', 'created_at', 'updated_at', 'final_amount_paid', 'payment_status']);

        $walletBalance = (float) ($user->wallet_balance ?? 0);

        $walletTransactions = DB::table('user_wallet_transactions')
            ->where('user_id', $userId)
            ->orderBy('id', 'desc')
            ->limit(20)
            ->get(['id', 'amount', 'type', 'created_at']);

        $documents = DB::table('user_documents')
            ->join('documents', 'documents.id', '=', 'user_documents.document_id')
            ->where('user_documents.user_id', $userId)
            ->select([
                'user_documents.id',
                'user_documents.document_verification_status',
                'user_documents.expire_date',
                'user_documents.document_number',
                'user_documents.status',
            ])
            ->get();
        
        $userWalletSummary = [
            'total_credited'       => DB::table('user_wallet_transactions')
                                        ->where('user_id', $userId)
                                        ->where('type', 1) 
                                        ->sum('amount'),
        
            'total_debited'        => DB::table('user_wallet_transactions')
                                        ->where('user_id', $userId)
                                        ->where('type', 2)
                                        ->sum('amount'),
        
            'currency'             => DB::table('users')
                                        ->join('countries', 'users.country_id', '=', 'countries.id')
                                        ->where('users.id', $userId)
                                        ->value('countries.isoCode'),
        
            'last_transaction_date'=> DB::table('user_wallet_transactions')
                                        ->where('user_id', $userId)
                                        ->latest('id')
                                        ->value('created_at'),
        ];
        
        return response()->json([
            'generated_at' => Carbon::now()->toIso8601String(),
            'entity' => [
                'type' => 'user',
                'id' => $user->id,
                'merchant_id' => $user->merchant_id ?? null,
                'name' => trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')),
                'status' => $user->UserStatus,
                'wallet_balance' => $walletBalance,
                'email'=> $user->email,
                'phone'=> $user->UserPhone,
                'total_trips_taken'=> $user->total_trips,
                'country'=> !empty($user->Country) ? $user->Country->CountryName : $user->CountryArea->Country->CountryName
            ],
            'trips' => $bookings,
            'wallet_transactions' => $walletTransactions,
            'documents' => $documents,
            'user_wallet_summary'=> $userWalletSummary
        ]);
    }

    protected function driverActivity(int $driverId)
    {
        $driver = \App\Models\Driver::where('id', $driverId)->first();
        if (!$driver) {
            abort(404, 'Driver not found');
        }

        $bookings = DB::table('bookings')
            ->where('driver_id', $driverId)
            ->orderBy('id', 'desc')
            ->limit(20)
            ->get(['id', 'booking_status', 'created_at', 'updated_at', 'final_amount', 'payment_status']);

        $walletTransactions = DB::table('driver_wallet_transactions')
            ->where('driver_id', $driverId)
            ->orderBy('id', 'desc')
            ->limit(20)
            ->get(['id', 'amount', 'transaction_type', 'payment_method_id', 'created_at']);

        $documents = DB::table('driver_documents')
            ->join('documents', 'documents.id', '=', 'driver_documents.document_id')
            ->where('driver_documents.driver_id', $driverId)
            ->select([
                'driver_documents.id',
                'documents.document_name',
                'driver_documents.document_verification_status',
                'driver_documents.expire_date',
            ])
            ->get();
            
        $driverWalletSummary = [
            'total_credited'       => DB::table('driver_wallet_transactions')
                                        ->where('driver_id', $driverId)
                                        ->where('transaction_type', 1) 
                                        ->sum('amount'),
        
            'total_debited'        => DB::table('driver_wallet_transactions')
                                        ->where('driver_id', $driverId)
                                        ->where('transaction_type', 2)
                                        ->sum('amount'),
        
            'currency'             => DB::table('drivers')
                                        ->join('country_areas', 'drivers.country_area_id', '=', 'country_areas.id')
                                        ->join('countries', 'country_areas.country_id', '=', 'countries.id')
                                        ->where('drivers.id', $driverId)
                                        ->value('countries.isoCode'),
        
            'last_transaction_date'=> DB::table('driver_wallet_transactions')
                                        ->where('driver_id', $driverId)
                                        ->latest('id')
                                        ->value('created_at'),
        ];

        return response()->json([
            'generated_at' => Carbon::now()->toIso8601String(),
            'entity' => [
                'type' => 'driver',
                'id' => $driver->id,
                'merchant_id' => $driver->merchant_id ?? null,
                'name' => trim(($driver->first_name ?? '') . ' ' . ($driver->last_name ?? '')),
                'online_offline' => $driver->online_offline,
                'free_busy' => $driver->free_busy,
                'wallet_balance' => (float) ($driver->wallet_money ?? 0),
                'total_trips' => (int) ($driver->total_trips ?? 0),
                'total_earnings' => (float) ($driver->total_earnings ?? 0),
                'email'=> $driver->email,
                'phone'=> $driver->phoneNumber
            ],
            'trips' => $bookings,
            'wallet_transactions' => $walletTransactions,
            'documents' => $documents,
            'driver_wallet_summary'=> $driverWalletSummary
        ]);
    }


    public function bookingExplanation(Request $request)
    {
        $this->assertAuthorized($request);
        $bookingId  = (int) $request->input('booking_id');
        $merchantId = (int) $request->input('merchant_id');
        if (!$bookingId || !$merchantId) {
            abort(422, 'booking_id and merchant_id are required');
        }

        /** @var Booking|null $booking */
        $booking = Booking::with([
            'User',
            'Driver',
            'DriverVehicle',
            'VehicleType',
            'CountryArea.Country',
            'ServiceType',
            'Segment',
            'PaymentMethod',
            'PriceCard.PriceCardValues.PricingParameter',
            'BookingDetail',
            'BookingRating',
            'BookingTransaction',
            'BookingDeliveryDetails',
            'BookingCoordinate'
        ])
            ->where('merchant_id', $merchantId)
            ->find($bookingId);

        if (!$booking) {
            abort(404, 'Booking not found for this merchant');
        }

        switch ((int) $booking->booking_status) {
            case 1000:
                $statusText = 'New / Pending driver';
                break;
            case 1001:
                $statusText = 'Scheduled / Driver accepted';
                break;
            case 1002:
                $statusText = 'Driver arriving';
                break;
            case 1003:
                $statusText = 'Driver arrived / Waiting at pickup';
                break;
            case 1004:
                $statusText = 'On trip';
                break;
            case 1005:
                $statusText = 'Completed';
                break;
            case 1006:
                $statusText = 'Cancelled by user';
                break;
            case 1007:
                $statusText = 'Cancelled by driver';
                break;
            case 1008:
                $statusText = 'Cancelled by admin';
                break;
            case 1016:
                $statusText = 'Expired';
                break;
            default:
                $statusText = 'Unknown (' . $booking->booking_status . ')';
                break;
        }

        // Waiting time parameter (parameterType = 9), mirrors bookingDetails logic
        $freeWaitingTime = null;
        if ($booking->PriceCard && $booking->PriceCard->PriceCardValues) {
            foreach ($booking->PriceCard->PriceCardValues as $pcv) {
                if (isset($pcv->PricingParameter) && $pcv->PricingParameter->parameterType == 9) {
                    $freeWaitingTime = $pcv->free_value;
                    break;
                }
            }
        }

        // Arrive timestamp when driver is waiting
        $arriveTimestamp = null;
        if ($booking->booking_status == 1003 && $booking->BookingDetail) {
            $arriveTimestamp = $booking->BookingDetail->arrive_timestamp;
        }

        // ETA from stored google response, mirrors bookingDetails logic
        $etaPickupAndDest = null;
        if ($booking->BookingDetail) {
            if ($booking->booking_status == 1002 && !empty($booking->BookingDetail->on_accept_google_response)) {
                $googleResponse   = json_decode($booking->BookingDetail->on_accept_google_response, true);
                $etaPickupAndDest = $googleResponse['total_time_text'] ?? null;
            } elseif ($booking->booking_status == 1004 && !empty($booking->BookingDetail->after_start_google_response)) {
                $googleResponse   = json_decode($booking->BookingDetail->after_start_google_response, true);
                $etaPickupAndDest = $googleResponse['time'] ?? null;
            }
        }

        // Waypoints
        $waypoints = !empty($booking->waypoints)
            ? json_decode($booking->waypoints, true)
            : [];

        // Delivery details, mirrors bookingDetails OTP/QR logic
        $deliveryDetails = [];
        if ($booking->Segment && $booking->Segment->slag === 'DELIVERY') {
            $deliveryDetails = $booking->BookingDeliveryDetails
                ? $booking->BookingDeliveryDetails->map(function ($d) {
                    return [
                        'id'          => $d->id,
                        'stop_no'     => $d->stop_no,
                        'drop_status' => $d->drop_status,
                        'otp_status'  => $d->otp_status,
                        'otp'         => $d->opt_for_verify,
                    ];
                })->values()
                : [];
        }

        // Tip details from BookingDetail
        $tipAmount      = $booking->BookingDetail->tip_amount ?? 0;
        $tipAlreadyPaid = $tipAmount > 0 ? 1 : 2;
        
        $statusByDriverForBooking = [
            1 => "No Action",
            2 => "Accepted",
            3 => "rejected",
            4 => "cancelled",
        ];
        $bookingRequestDriver = [];
        if(count($booking->BookingRequestDriver) > 0){
            $bookingRequestDriver = $booking->BookingRequestDriver->map(function($brd) use($booking,$statusByDriverForBooking){
                return [
                    'name'=> $brd->Driver->first_name,
                    'phone'=> $brd->Driver->phoneNumber,
                    'email'=> $brd->Driver->email,
                    'pickup_distance'=> !empty($brd->distance_from_pickup) && ($brd->distance_from_pickup != 0) ? round($brd->distance_from_pickup, 2) . ' ' : '0.00 ',
                    'dead_milage'=> (isset($brd->Booking->BookingDetail) && $booking->driver_id == $brd->Driver->id)?  round_number($brd->Booking->BookingDetail->dead_milage_distance, 2): 0,
                    'eta'=> !empty($brd->eta_at_pickup) ? $brd->eta_at_pickup : '------- ',
                    'request_status'  => $statusByDriverForBooking[$brd->request_status] ?? 'Unknown',
                ];
            })->values();
        }
        
        $plateform = [
            "1"=> "Application",
            "2"=> "Admin",
            "3"=> "Web"
        ];
        return response()->json([
            'booking_id'  => $booking->id,
            'merchant_id' => $booking->merchant_id,

            'basic' => [
                'status_code'         => $booking->booking_status,
                'status_text'         => $statusText,
                'segment'             => [
                    'id'   => optional($booking->Segment)->id,
                    'slag' => optional($booking->Segment)->slag,
                ],
                'service_type'        => [
                    'id'   => optional($booking->ServiceType)->id,
                    'name' => optional($booking->ServiceType)->ServiceName($merchantId),
                    'type' => optional($booking->ServiceType)->type,
                ],
                'vehicle_type'        => [
                    'id'    => optional($booking->VehicleType)->id,
                    'name'  => optional($booking->VehicleType)->vehicle_type_name,
                    'image' => optional($booking->VehicleType)->vehicleTypeImage,
                ],
                'area'                => [
                    'id'      => optional($booking->CountryArea)->id,
                    'name'    => optional($booking->CountryArea)->CountryAreaName,
                    'country' => [
                        'id'       => optional($booking->CountryArea->Country)->id,
                        'name'     => optional($booking->CountryArea->Country)->CountryName,
                        'iso_code' => optional($booking->CountryArea->Country)->isoCode,
                    ],
                ],
                'pickup_location'     => $booking->pickup_location,
                'pickup_latitude'     => $booking->pickup_latitude,
                'pickup_longitude'    => $booking->pickup_longitude,
                'drop_location'       => $booking->drop_location,
                'drop_latitude'       => $booking->drop_latitude,
                'drop_longitude'      => $booking->drop_longitude,
                'total_drop_location' => $booking->total_drop_location,
                'waypoints'           => $waypoints,
                'ploy_points'         => $booking->ploy_points,
                'is_in_drive'         => $booking->is_in_drive,
                'unique_id'           => $booking->unique_id,
                'request_from'      => $plateform[$booking->platform],
                'is_geofence'       => $booking->is_geofence == 0 ? false : true
            ],

            'user' => [
                'id'    => optional($booking->User)->id,
                'name'  => trim(optional($booking->User)->first_name . ' ' . optional($booking->User)->last_name),
                'phone' => optional($booking->User)->UserPhone,
                'email' => optional($booking->User)->email,
            ],

            'driver' => [
                'id'                => optional($booking->Driver)->id,
                'name'              => trim(optional($booking->Driver)->first_name . ' ' . optional($booking->Driver)->last_name),
                'phone'             => optional($booking->Driver)->phoneNumber,
                'email'             => optional($booking->Driver)->email,
                'rating'            => optional($booking->Driver)->rating,
                'current_latitude'  => optional($booking->Driver)->current_latitude,
                'current_longitude' => optional($booking->Driver)->current_longitude,
                'profile_image'     => optional($booking->Driver)->profile_image,
            ],

            'driver_vehicle' => [
                'id'                      => optional($booking->DriverVehicle)->id,
                'vehicle_color'           => optional($booking->DriverVehicle)->vehicle_color,
                'number_plate'            => optional($booking->DriverVehicle)->vehicle_number_plate,
                'number_plate_image'      => optional($booking->DriverVehicle)->vehicle_number_plate_image,
                'vehicle_seat_view_image' => optional($booking->DriverVehicle)->vehicle_seat_image,
                'vehicle_side_view_image' => optional($booking->DriverVehicle)->vehicle_side_view_image,
            ],

            'pricing' => [
                'estimate_bill'         => $booking->estimate_bill,
                'estimate_distance'=> $booking->estimate_distance,
                'estimate_bill_display' => optional($booking->CountryArea->Country)->isoCode . ' ' . $booking->estimate_bill,
                'final_amount_paid'     => $booking->final_amount_paid,
                'final_distance'=>$booking->travel_distance,
                'payment_status'        => $booking->payment_status,
                'payment_method'        => [
                    'id'   => optional($booking->PaymentMethod)->id,
                    'name' => optional($booking->PaymentMethod)->MethodName($merchantId)
                        ?? optional($booking->PaymentMethod)->payment_method,
                    'icon' => optional($booking->PaymentMethod)->icon,
                ],
                'tip_amount'            => $tipAmount,
                'tip_already_paid'      => $tipAlreadyPaid,
                'price_card'            => [
                    'id'            => optional($booking->PriceCard)->id,
                    'name'          => optional($booking->PriceCard)->price_card_name,
                    'base_fare'     => optional($booking->PriceCard)->base_fare,
                    'free_time'     => optional($booking->PriceCard)->free_time,
                    'free_distance' => optional($booking->PriceCard)->free_distance,
                    'parameters'    => optional($booking->PriceCard)->PriceCardValues
                        ? $booking->PriceCard->PriceCardValues->map(function ($v) {
                            return [
                                'id'              => $v->id,
                                'parameter_type'  => optional($v->PricingParameter)->parameterType,
                                'parameter_name'  => optional($v->PricingParameter)->parameterName,
                                'parameter_price' => $v->parameter_price,
                                'free_value'      => $v->free_value,
                            ];
                        })->values()
                        : [],
                ],
            ],

            'waiting_time' => [
                'free_waiting_time_enable' => !empty($freeWaitingTime),
                'free_waiting_time'        => $freeWaitingTime,
                'arrive_timestamp'         => $arriveTimestamp,
            ],

            'booking_detail' => [
                'otp_enable'                  => $booking->otp_enable,
                'ride_otp'                    => $booking->ride_otp,
                'cancelable'                  => in_array($booking->booking_status, [1001, 1002, 1003]),
                'eta_pickup_and_dest'         => $etaPickupAndDest,
                'additional_notes'            => $booking->additional_notes,
                'admin_notes'                 => $booking->booking_closure,
                'on_accept_google_response'   => optional($booking->BookingDetail)->on_accept_google_response,
                'after_start_google_response' => optional($booking->BookingDetail)->after_start_google_response,
            ],

            'delivery' => [
                'is_delivery'  => optional($booking->Segment)->slag === 'DELIVERY',
                'drop_details' => $deliveryDetails,
            ],

            'rating' => [
                'user_rating'    => optional($booking->BookingRating)->user_rating_points,
                'driver_rating'  => optional($booking->BookingRating)->driver_rating_points,
                'user_comment'   => optional($booking->BookingRating)->user_comment,
                'driver_comment' => optional($booking->BookingRating)->driver_comment,
            ],

            'timeline' => [
                'created_at' => $booking->created_at,
                'updated_at' => $booking->updated_at,
                'later_date' => $booking->later_booking_date,
                'later_time' => $booking->later_booking_time,
                'history'    => !empty($booking->booking_status_history)
                    ? json_decode($booking->booking_status_history, true)
                    : [],
            ],
            'booking_request_drivers'=> $bookingRequestDriver,
            'coordinate_data'=>[
                'coordinates'=> $booking->BookingCoordinate->coordinates,
                'booking_distance_log' => isset($booking->BookingDetail)? json_decode($booking->BookingDetail->distance_log) : null
            ],
            'fare_breakup'=> json_decode($booking->bill_details,true),
        ]);
    }
}

