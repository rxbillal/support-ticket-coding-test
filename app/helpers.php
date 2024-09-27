<?php

use App\Models\Conversation;
use App\Models\Setting;
use App\Models\User;
use App\Models\UserNotification;
use App\Repositories\NotificationRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail as Email;
use Spatie\Permission\Models\Role;

//append new language using file_put_contents method when admin add language
const LANGUAGES = [
    'en' => 'English',
    'es' => 'Spanish',
    'fr' => 'French',
    'de' => 'German',
    'ru' => 'Russian',
    'pt' => 'Portuguese',
    'ar' => 'Arabic',
    'zh' => 'Chinese',
    'tr' => 'Turkish',
];

/**
 * @return int
 */
function getLoggedInUserId()
{
    return Auth::id();
}

/**
 * @return User
 */
function getLoggedInUser()
{
    return Auth::user();
}

/**
 * @param $key
 *
 * @return mixed
 */
function getSettingValue($key)
{
    return Setting::where('key', $key)->value('value');
}

/**
 * @return array
 */
function getNotifications()
{
    /** @var NotificationRepository $notificationRepo */
    $notificationRepo = app(NotificationRepository::class);

    return $notificationRepo->getNotifications();
}

/**
 * @param $userId
 * @param $name
 *
 *
 * @return string
 */
function getUserImageInitial($userId, $name)
{
    return getAvatarUrl()."?name=$name&size=100&rounded=true&color=fff&background=".getRandomColor($userId);
}

/**
 * return avatar url.
 *
 * @return string
 */
function getAvatarUrl()
{
    return 'https://ui-avatars.com/api/';
}

/**
 * return random color.
 *
 * @param  int  $userId
 *
 * @return string
 */
function getRandomColor($userId)
{
    $colors = ['329af0', 'fc6369', 'ffaa2e', '42c9af', '7d68f0'];
    $index = $userId % 5;

    return $colors[$index];
}

/**
 * @return mixed|string
 */
function getAppName()
{
    static $appNameSetting;

    if (! empty($appNameSetting)) {
        return $appNameSetting;
    }

    $record = Setting::where('key', '=', 'app_name')->first();
    $appNameSetting = (! empty($record)) ? $record->value : config('app.name');

    return $appNameSetting;
}

/**
 * @param  int  $gender
 *
 * @return string
 */
function getGender($gender)
{
    if ($gender == 1) {
        return 'male';
    }
    if ($gender == 2) {
        return 'female';
    }

    return '';
}

/**
 * @param  int  $status
 *
 * @return string
 */
function getOnOffClass($status)
{
    if ($status == 1) {
        return 'online';
    }

    return 'offline';
}

/**
 * @param $url
 *
 *
 * @return mixed
 */
function isValidURL($url)
{
    return filter_var($url, FILTER_VALIDATE_URL);
}

/**
 * @return mixed
 */
function getAdminUserId()
{
    return User::with([
        'roles' => function ($q) {
            $q->where('name', 'Admin');
        },
    ])->oldest()->first()->id;
}

/**
 * @param  array  $models
 * @param  string  $columnName
 * @param  int  $id
 *
 * @return bool
 */
function canDelete($models, $columnName, $id)
{
    foreach ($models as $model) {
        $result = $model::where($columnName, $id)->exists();
        if ($result) {
            return true;
        }
    }

    return false;
}

/**
 * @return mixed
 */
function getLoggedInUserRoleId()
{
    return (Auth::check()) ? Auth::user()->roles()->first()->id : false;
}

/**
 * @return mixed
 */
function getAdminRoleId()
{
    return Role::whereName('Admin')->first()->id;
}

/**
 * @return mixed
 */
function getAgentRoleId()
{
    return Role::whereName('Agent')->first()->id;
}

/**
 * @return mixed
 */
function getCustomerRoleId()
{
    return Role::whereName('Customer')->first()->id;
}

function getRoleId($role)
{
    return Role::whereName($role)->first()->id;
}

/**
 * @return bool
 */
function checkLoggedInUserRole()
{
    if (! Auth::check()) {
        return false;
    }

    return Auth::user()->hasRole('Admin') || Auth::user()->hasRole('Agent') || Auth::user()->hasRole('Customer');
}

/**
 * @return string
 */
function generateNewColor()
{
    return '#'.dechex(rand(0x000000, 0xFFFFFF));
}

/**
 * @param $index
 *
 * @return string
 */
function getNewColor($index)
{
    $colorArr = [
        '#3d5a80',
        '#98c1d9',
        '#14999e',
        '#fb8500',
        '#ffb703',
        '#f94144',
        '#FF6F91',
        '#FF9671',
        '#d8105a',
        '#710000',
        '#3772ff',
        '#485696',
        '#37423d',
        '#2e294e',
        '#331772',
        '#2d00f7',
        '#248232',
        '#2ba84a',
        '#ff86c8',
        '#7de2d1',
        '#774936',
        '#008B81',
        '#4FFBDF',
        '#00D2FC',
        '#3772ff',
    ];

    if (30 < $index) {
        return generateNewColor();
    }

    return $colorArr[$index];
}

function addNotification($data){
    
    $notificationRecord = [
        'title'       => $data[0],
        'type'        => $data[1],
        'description' => $data[2],
        'user_id'     => $data[3],
    ];
    
    UserNotification::create($notificationRecord);
}

/**
 * @param $url
 *
 * @return string
 */
function mediaUrlEndsWith($url)
{
    $extension = pathinfo($url, PATHINFO_EXTENSION);
    if ($extension === 'pdf') {
        return asset('assets/img/pdf_icon.png');
    } elseif (in_array($extension, ['doc', 'docx'])) {
        return asset('assets/img/doc_icon.png');
    } elseif (in_array($extension, ['xls', 'xlsx'])) {
        return asset('assets/img/xls_icon.png');
    } elseif ($extension === 'zip') {
        return asset('assets/img/zip_icon.png');
    } elseif (in_array($extension, ['text', 'txt'])) {
        return asset('assets/img/txt_icon.png');
    } elseif (in_array($extension, ['jpeg', 'jpg', 'png', 'gif', 'jfif', 'exif', 'tiff', 'bmp', 'webp'])) {
        return $url;
    } else {
        return asset('assets/img/file_icon.png');
    }
}

if (! function_exists('canUserReplyTicket')) {

    /**
     * @param  \App\Models\Ticket  $ticket
     *
     * @return bool
     */
    function canUserReplyTicket($ticket)
    {
        $isEditable = checkLoggedInUserRole() && (getLoggedInUserId() == $ticket->user->id || getLoggedInUserId() == getAdminRoleId()) && $ticket->status != \App\Models\Ticket::STATUS_CLOSED;

        if (! $isEditable && Auth::check()) {
            $isEditable = Auth::user()->ticket()
                ->where('tickets.id', '=', $ticket->id)
                ->where('tickets.status', '!=', \App\Models\Ticket::STATUS_CLOSED)
                ->exists();
        }

        return $isEditable;
    }
}

/**
 * @param $url
 *
 * @return int
 */
function detectURL($url)
{
    if (strpos($url, 'youtube.com/watch?v=') > 0) {
        return Conversation::YOUTUBE_URL;
    }

    return 0;
}

if (! function_exists('getConversationCount')) {

    /**
     * @return mixed
     */
    function getConversationCount()
    {
        $authUser = Auth::user();
        $authId = $authUser->id;
        $conversation = Conversation::leftJoin('users as u', 'u.id', '=',
            \DB::raw("if(from_id = $authId, to_id, from_id)"))
            ->leftJoin('message_action as ma', function (JoinClause $join) use ($authId) {
                $join->on('ma.deleted_by', '=', \DB::raw("$authId"));
                $join->on('ma.conversation_id', '=', 'conversations.id');
            })
            ->whereNull('u.deleted_at');

        $conversation->where(function (Builder $q) use ($authId) {
            $q->where('to_id', '=', $authId)->where('status', '=', 0);
        });

        $conversation->where(function (Builder $q) {
            $q->whereColumn('ma.conversation_id', '!=', 'conversations.id')
                ->orWhereNull('ma.conversation_id');
        });

        return $conversation->count();
    }
}

if (! function_exists('getNotification')) {

    /**
     * @return \App\Models\UserNotification[]|Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Query\Builder[]|\Illuminate\Support\Collection
     */
    function getNotification()
    {
        return \App\Models\UserNotification::whereReadAt(null)->where('user_id',
            getLoggedInUserId())->orderByDesc('created_at')->get();
    }
}

if (! function_exists('getNotificationIcon')) {

    /**
     * @param $notificationFor
     *
     * @return string
     */
    function getNotificationIcon($notificationFor)
    {
        switch ($notificationFor) {
            case 1:
                return 'fas fa-ticket-alt';
            case 2:
                return 'fas fa-paperclip';
            case 3:
                return 'fas fa-retweet';
            default:
                return 'fa fa-inbox';
        }
    }
}

function sendEmailToAdmin($mailView, $subject, $data)
{
    /** @var User $user */
    $user = User::whereId(getAdminUserId())->firstOrFail();
    if (! $user->email_update) {
        return true;
    }
    $data['user_name'] = $user->name;
    Email::to($user->email)
        ->send(new \App\Mail\TicketsMail($mailView,
            $subject,
            $data));
}

function sendEmailToAgent($agentId, $mailView, $subject, $data)
{
    /** @var User $user */
    $user = User::whereId($agentId)->firstOrFail();
    if (! $user->email_update) {
        return true;
    }
    $data['user_name'] = $user->name;

    Email::to($user->email)
        ->send(new \App\Mail\TicketsMail($mailView,
            $subject,
            $data));
}

function sendEmailToCustomer($customerId, $mailView, $subject, $data)
{
    /** @var User $user */
    $user = User::whereId($customerId)->firstOrFail();
    if (! $user->email_update) {
        return true;
    }
    $data['user_name'] = $user->name;
    
    Email::to($user->email)
        ->send(new \App\Mail\TicketsMail($mailView,
            $subject,
            $data));
}

/**
 * @return mixed
 */
function getCurrentVersion()
{
    $composerFile = file_get_contents('../composer.json');
    $composerData = json_decode($composerFile, true);
    $currentVersion = $composerData['version'];

    return $currentVersion;
}
