<?php

use App\Http\Controllers\API;
use App\Http\Controllers\API\BlockUserAPIController;
use App\Http\Controllers\API\ChatAPIController;
use App\Http\Controllers\API\NotificationController;
use App\Http\Controllers\API\UserAPIController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\CustomerDashboardController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DownloadMediaController;
use App\Http\Controllers\FAQController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TicketReplayController;
use App\Http\Controllers\TranslationManagerController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserNotificationController;
use App\Http\Controllers\WebUserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();

Route::middleware(['xss'])->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('web.home');
    Route::get('/login/{provider}',[SocialAuthController::class, 'redirectToSocial'])->name('social.login');
    Route::get('/login/{provider}/callback',[SocialAuthController::class, 'handleSocialCallback']);
    Route::get('/get-public-tickets', [HomeController::class, 'getPublicTickets'])->name('get.public.tickets');
    Route::get('/submit-ticket', [HomeController::class, 'createTicket'])->name('web.submit_ticket');
    Route::post('/store-ticket', [TicketController::class, 'webStore'])->name('web.ticket.store');
    Route::get('/ticket-details/{ticket_id}', [TicketController::class, 'ticketSuccessView'])->name('web.ticket_successful');
    Route::get('/search-ticket', [HomeController::class, 'searchTicketForm'])->name('web.search_ticket_form');
    Route::get('ticket/{ticket_id}', [TicketController::class, 'viewTicket'])->name('web.ticket.view');
    Route::get('tickets', [TicketController::class, 'showAllPublicTickets'])->name('public.tickets');
    Route::get('categories-list', [CategoryController::class, 'showAllCategories'])->name('categories-list');
    Route::get('/download-media/{mediaItem}', [DownloadMediaController::class, 'show'])->name('download.media');
    
    Route::get('/ticket', [HomeController::class, 'searchTicket'])->name('web.search_ticket');
    Route::get('/faqs', [HomeController::class, 'faqs'])->name('web.faqs');

    // Landing page conversation routes
    Route::post('/store-chat-user', [WebUserController::class, 'storeChatUser'])->name('web.storeChatUser');
    Route::get('/get-assign-agent', [WebUserController::class, 'getAssignAgent'])->name('web.getAssignAgent');
    Route::post('web-read-message', [WebUserController::class, 'readMessages']);
});

Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'xss', 'role:Admin']], function () {
    // Route for laravel log viewer
    Route::get('logs', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index']);

    Route::get('/tickets/create', [TicketController::class, 'create'])->name('ticket.create');
    Route::post('tickets', [TicketController::class, 'store'])->name('ticket.store');
    Route::get('tickets/media/{id}', [TicketController::class, 'downloadAttachment'])->name('admin-download-attachment');

    //users
    Route::get('/users', [UserController::class, 'index'])->name('user.index');
    Route::get('/agents', [UserController::class, 'index'])->name('agent.index');
    Route::get('/agents/create', [UserController::class, 'create'])->name('user.create');
    Route::post('users', [UserController::class, 'store'])->name('user.store');
    Route::get('agents/{user}', [UserController::class, 'show'])->name('user.show');
    Route::get('agents/{user}/edit', [UserController::class, 'edit'])->name('agent.edit');
    Route::put('users/{user}', [UserController::class, 'update'])->name('user.update');
    Route::delete('users/{user}', [UserController::class, 'destroy'])->name('user.destroy');

    // customers
    Route::get('/customers', [UserController::class, 'customers'])->name('customer.index');
    Route::get('/customers/create', [UserController::class, 'create'])->name('customer.create');
    Route::get('customers/{user}', [UserController::class, 'show'])->name('customer.show');
    Route::get('customers/{user}/edit', [UserController::class, 'edit'])->name('customer.edit');

    // Admin Routes
    Route::resource('admins', AdminController::class);
    Route::get('admins', [AdminController::class, 'index'])->name('admins.index');
    Route::get('admins/create', [AdminController::class, 'create'])->name('admins.create');
    Route::get('admins/{user}/edit', [AdminController::class, 'edit'])->name('admins.edit');
    Route::put('admins/{user}', [AdminController::class, 'update'])->name('admins.update');
    Route::delete('admins/{user}', [AdminController::class, 'destroy'])->name('admins.destroy');

    Route::get('ticket-by-user/{id}', [TicketController::class, 'ticketByUser'])->name('user.ticket');

    //categories
    Route::get('/categories', [CategoryController::class, 'index'])->name('category.index');
    Route::post('categories', [CategoryController::class, 'store'])->name('category.store');
    Route::get('categories/{category}', [CategoryController::class, 'show'])->name('category.show');
    Route::get('categories/{category}/edit', [CategoryController::class, 'edit'])->name('category.edit');
    Route::put('categories/{category}', [CategoryController::class, 'update'])->name('category.update');
    Route::delete('categories/{category}', [CategoryController::class, 'destroy'])->name('category.destroy');

    // FAQ routes
    Route::get('faqs', [FAQController::class, 'index'])->name('faqs.index');
    Route::post('faqs', [FAQController::class, 'store'])->name('faqs.store');
    Route::post('faqs/upload', [FAQController::class, 'upload'])->name('faqs.upload');
    Route::get('faqs/{faq}', [FAQController::class, 'show'])->name('faqs.show');
    Route::get('faqs/{faq}/edit', [FAQController::class, 'edit'])->name('faqs.edit');
    Route::put('faqs/{faq}', [FAQController::class, 'update'])->name('faqs.update');
    Route::delete('faqs/{faq}', [FAQController::class, 'destroy'])->name('faqs.destroy');

    // setting routes
    Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('settings', [SettingController::class, 'update'])->name('settings.update');

    Route::delete('ticket-attachment-delete', [TicketController::class, 'attachmentDelete'])->name('ticket.attachment');

    /** Translation Manager Routes */
    Route::group(['prefix' => 'translation-manager'], function (){
        Route::get('/', [TranslationManagerController::class, 'index'])->name('translation-manager.index');
        Route::post('/', [TranslationManagerController::class, 'store'])->name('translation-manager.store');
        Route::post('/update', [TranslationManagerController::class, 'update'])->name('translation-manager.update');
    });
});

Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'xss', 'role:Admin']], function () {

    //dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/category-ticket-chart', [DashboardController::class, 'categoryTicketChart'])->name('category-ticket-chart');
    Route::get('/ticket-chart', [DashboardController::class, 'ticketChart'])->name('ticket-chart');
    Route::get('/agent-ticket-report', [DashboardController::class, 'agentTicketReport'])->name('agent-ticket-report');

    //profile menu
    Route::get('profile', [UserController::class, 'editProfile']);
    Route::post('change-password', [UserController::class, 'changePassword']);
    Route::post('profile-update', [UserController::class, 'profileUpdate']);

    //tickets
    Route::get('/tickets', [TicketController::class, 'index'])->name('ticket.index');
    Route::get('/tickets/{ticket}/edit-assignees', [TicketController::class, 'editAssignee'])->name('ticket.edit-assignee');
    Route::get('tickets/{ticket}', [TicketController::class, 'show'])->name('ticket.show');
    Route::get('tickets/{ticket}/edit', [TicketController::class, 'edit'])->name('ticket.edit');
    Route::put('tickets/{ticket}', [TicketController::class, 'update'])->name('ticket.update');
});

Route::group(['prefix' => 'agent', 'middleware' => ['auth', 'xss', 'role:Agent']], function () {

    //dashboard
    Route::get('/dashboard', [DashboardController::class, 'agentDashBoard'])->name('agent.dashboard');
    Route::get('/category-ticket-chart',
        [DashboardController::class, 'categoryTicketChart'])->name('agent.category-ticket-chart');
    Route::get('/ticket-chart', [DashboardController::class, 'ticketChart'])->name('agent.ticket-chart');

    //profile menu
    Route::get('profile', [UserController::class, 'editProfile']);
    Route::post('change-password', [UserController::class, 'changePassword']);
    Route::post('profile-update', [UserController::class, 'profileUpdate']);

    //tickets
    Route::get('/tickets', [TicketController::class, 'index'])->name('agent.ticket.index');
    Route::get('tickets/{ticket}', [TicketController::class, 'show'])->name('agent.ticket.show');
    Route::get('tickets/{ticket_id}/edit', [TicketController::class, 'edit'])->name('agent.ticket.edit');
    Route::put('tickets/{ticket}', [TicketController::class, 'update'])->name('agent.ticket.update');
    Route::get('tickets/media/{id}', [TicketController::class, 'downloadAttachment'])->name('agent-download-attachment');
});

Route::group(['middleware' => ['auth', 'role:Admin|Agent|Customer', 'xss']], function () {
    Route::post('/add-reply', [TicketReplayController::class, 'store'])->name('ticket.add-reply');
    Route::put('/reply-update/{ticketReplay}', [TicketReplayController::class, 'update'])->name('ticket.reply.update');
    Route::delete('reply/{ticketReplay}', [TicketReplayController::class, 'destroy'])->name('replay.destroy');
    Route::delete('/ticket-delete', [TicketController::class, 'delete'])->name('ticket.delete');
    Route::post('/unassigned-agent', [TicketController::class, 'unassignedFromTicket'])->name('ticket.unassigned');
    Route::delete('/ticket-replay-attachment/{media}', [TicketReplayController::class, 'deleteAttachment'])
        ->name('ticket.replay.attachment.delete');
    Route::put('/ticket-reply-attachment', [TicketReplayController::class, 'addAttachment'])->name('ticket-reply-add-attachment');
    Route::post('change-language', [UserController::class, 'changeLanguage'])->name('change.language');

    Route::get('/tickets/{ticket}/get-attachments',
        [TicketController::class, 'getAttachment'])->name('ticket.get-attachments');
    Route::post('/tickets/{ticket}/add-attachment',
        [TicketController::class, 'addAttachment'])->name('ticket.add-attachment');
    Route::post('/tickets/{media}/delete-attachment', [TicketController::class, 'deleteAttachment'])
        ->name('tickets.delete-attachment');
    Route::put('/ticket-status/{ticket}', [TicketController::class, 'changeStatus'])->name('ticket-status.update');

    // Read header notification
    Route::post('/notification/{notification}/read',
        [UserNotificationController::class, 'readNotification'])->name('read-notification');
    Route::post('/read-all-notification', [UserNotificationController::class, 'readAllNotification'])->name('read-all-notification');
    
    // Email setting
    Route::get('/email-update-setting', [UserController::class, 'getEmailUpdateSetting'])->name('get.email-update');
    Route::post('/email-update-setting', [UserController::class, 'setEmailUpdateSetting'])->name('set.email-update');
});

Route::group(['namespace' => 'API'], function () {
    Route::post('send-message', [ChatAPIController::class, 'sendMessage'])->name('conversations.store');
    Route::get('users/{id}/conversation', [ChatAPIController::class, 'getConversation']);
    Route::get('user/{id}/conversation', [ChatAPIController::class, 'getFrontConversation']);
    Route::post('assign-to-agent', [UserAPIController::class, 'assignAgent']);
    Route::post('update-last-seen', [UserAPIController::class, 'updateLastSeen']);
});

Route::group(['middleware' => ['auth', 'xss']], function () {
    //view routes
    Route::get('/conversations', [ChatController::class, 'index'])->name('conversations')->middleware('role:Admin|Agent');
    Route::get('profile', [UserController::class, 'getProfile']);
    Route::group(['namespace' => 'API'], function () {
//        Route::get('logout', [API\Auth\LoginController::class, 'logout']);

        //get all user list for chat
        Route::get('users-list', [UserAPIController::class, 'getUsersList']);
        Route::get('get-users', [UserAPIController::class, 'getUsers']);
        Route::delete('remove-profile-image', [UserAPIController::class, 'removeProfileImage']);
        Route::get('conversations/{ownerId}/archive-chat', [UserAPIController::class, 'archiveChat']);

        Route::get('get-profile', [UserAPIController::class, 'getProfile']);
        Route::get('conversations-list', [ChatAPIController::class, 'getLatestConversations']);
        Route::get('archive-conversations', [ChatAPIController::class, 'getArchiveConversations']);
        Route::post('read-message', [ChatAPIController::class, 'updateConversationStatus']);
        Route::post('file-upload', [ChatAPIController::class, 'addAttachment'])->name('file-upload');
        Route::post('image-upload', [ChatAPIController::class, 'imageUpload'])->name('image-upload');
        Route::get('conversations/{userId}/delete', [ChatAPIController::class, 'deleteConversation']);
        Route::post('conversations/message/{conversation}/delete', [ChatAPIController::class, 'deleteMessage']);
        Route::post('conversations/{conversation}/delete', [ChatAPIController::class, 'deleteMessageForEveryone']);
        Route::get('/conversations/{conversation}', [ChatAPIController::class, 'show']);
        
        // Conversation request route
        Route::post('send-chat-request', [ChatAPIController::class, 'sendChatRequest'])->name('send-chat-request');
        Route::post('accept-chat-request', [ChatAPIController::class, 'acceptChatRequest'])->name('accept-chat-request');
        Route::post('decline-chat-request', [ChatAPIController::class, 'declineChatRequest'])->name('decline-chat-request');

        /** BLock-Unblock User */
        Route::put('users/{user}/block-unblock', [BlockUserAPIController::class, 'blockUnblockUser']);
        Route::get('blocked-users', [BlockUserAPIController::class, 'blockedUsers']);

        Route::get('users-blocked-by-me', [BlockUserAPIController::class, 'blockUsersByMe']);
        Route::get('notification/{notification}/read', [NotificationController::class, 'readNotification']);
        Route::get('notification/read-all', [NotificationController::class, 'readAllNotification']);

        //report user
        Route::post('report-user', [API\ReportUserController::class, 'store'])->name('report-user.store');
    });
});

Route::group(['prefix' => 'customer', 'middleware' => ['auth', 'xss', 'role:Customer']], function () {
    Route::get('dashboard', [CustomerDashboardController::class, 'index'])->name('customer.dashboard');

    Route::get('tickets', [CustomerDashboardController::class, 'viewCustomerTicket'])->name('customer.myTicket');
    Route::get('ticket/create', [CustomerDashboardController::class, 'createTicket'])->name('customer.create.ticket');
    Route::post('ticket', [CustomerDashboardController::class, 'storeTicket'])->name('customer.ticket.store');
    Route::get('ticket/{ticket_id}/edit',
        [CustomerDashboardController::class, 'editCustomerTicket'])->name('customer.editTicket');
    Route::put('ticket/{ticket}',
        [CustomerDashboardController::class, 'updateCustomerTicket'])->name('customer.updateTicket');
    Route::get('tickets/{id}', [TicketController::class, 'show'])->name('ticket.view');
    Route::get('tickets/media/{id}', [TicketController::class, 'downloadAttachment'])->name('customer-download-attachment');

    Route::get('profile', [UserController::class, 'editProfile']);
    Route::post('change-password', [UserController::class, 'changePassword']);
    Route::post('profile-update', [UserController::class, 'profileUpdate']);
});

// Upgrade Routes File
require __DIR__.'/upgrade.php';
