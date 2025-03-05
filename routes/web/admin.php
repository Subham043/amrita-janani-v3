<?php

use App\Modules\Account\Controllers\AdminAccountController;
use App\Modules\Audios\Controllers\AudioAccessController;
use App\Modules\Audios\Controllers\AudioController;
use App\Modules\Audios\Controllers\AudioReportController;
use App\Modules\Audios\Controllers\AudioTrashController;
use App\Modules\Authentication\Controllers\AdminForgotPasswordController;
use App\Modules\Authentication\Controllers\AdminLoginController;
use App\Modules\Authentication\Controllers\AdminLogoutController;
use App\Modules\Authentication\Controllers\AdminResetPasswordController;
use App\Modules\Banners\Controllers\BannerCreateController;
use App\Modules\Banners\Controllers\BannerDeleteController;
use App\Modules\Banners\Controllers\BannerPaginateController;
use App\Modules\Banners\Controllers\BannerQuoteCreateController;
use App\Modules\Banners\Controllers\BannerQuoteDeleteController;
use App\Modules\Banners\Controllers\BannerQuotePaginateController;
use App\Modules\Dashboard\Controllers\DashboardController;
use App\Modules\Documents\Controllers\DocumentAccessController;
use App\Modules\Documents\Controllers\DocumentController;
use App\Modules\Documents\Controllers\DocumentReaderController;
use App\Modules\Documents\Controllers\DocumentReportController;
use App\Modules\Documents\Controllers\DocumentTrashController;
use App\Modules\Enquiries\Controllers\EnquiryDeleteController;
use App\Modules\Enquiries\Controllers\EnquiryExportController;
use App\Modules\Enquiries\Controllers\EnquiryPaginateController;
use App\Modules\Enquiries\Controllers\EnquiryReplyController;
use App\Modules\Enquiries\Controllers\EnquiryViewController;
use App\Modules\FAQs\Controllers\FAQCreateController;
use App\Modules\FAQs\Controllers\FAQDeleteController;
use App\Modules\FAQs\Controllers\FAQPaginateController;
use App\Modules\FAQs\Controllers\FAQUpdateController;
use App\Modules\Images\Controllers\ImageAccessController;
use App\Modules\Images\Controllers\ImageController;
use App\Modules\Images\Controllers\ImageReportController;
use App\Modules\Images\Controllers\ImageTrashController;
use App\Modules\Languages\Controllers\LanguageCreateController;
use App\Modules\Languages\Controllers\LanguageDeleteController;
use App\Modules\Languages\Controllers\LanguageExportController;
use App\Modules\Languages\Controllers\LanguagePaginateController;
use App\Modules\Languages\Controllers\LanguageUpdateController;
use App\Modules\Languages\Controllers\LanguageViewController;
use App\Modules\Pages\Controllers\PageController;
use App\Modules\Users\Controllers\UserCreateController;
use App\Modules\Users\Controllers\UserDeleteController;
use App\Modules\Users\Controllers\UserExportController;
use App\Modules\Users\Controllers\UserMailTestController;
use App\Modules\Users\Controllers\UserMultiDeleteController;
use App\Modules\Users\Controllers\UserMultiStatusToggleController;
use App\Modules\Users\Controllers\UserPaginateController;
use App\Modules\Users\Controllers\UserPreviledgeToggleController;
use App\Modules\Users\Controllers\UserStatusToggleController;
use App\Modules\Users\Controllers\UserUpdateController;
use App\Modules\Users\Controllers\UserViewController;
use App\Modules\Videos\Controllers\VideoAccessController;
use App\Modules\Videos\Controllers\VideoController;
use App\Modules\Videos\Controllers\VideoReportController;
use App\Modules\Videos\Controllers\VideoTrashController;
use Illuminate\Support\Facades\Route;


Route::prefix('/admin')->group(function () {
    Route::prefix('/auth')->middleware(['guest'])->group(function () {
        Route::get('/login', [AdminLoginController::class, 'get', 'as' => 'admin.login'])->name('signin');
        Route::post('/authenticate', [AdminLoginController::class, 'post', 'as' => 'admin.authenticate'])->name('authenticate');
        Route::get('/forgot-password', [AdminForgotPasswordController::class, 'get', 'as' => 'admin.forgot_password'])->name('forgotPassword');
        Route::post('/forgot-password', [AdminForgotPasswordController::class, 'post', 'as' => 'admin.requestForgotPassword'])->name('requestForgotPassword');
        Route::prefix('/reset-password/{token}')->middleware(['signed'])->group(function () {
            Route::get('/', [AdminResetPasswordController::class, 'get', 'as' => 'admin.reset_password'])->name('reset_password');
            Route::post('/', [AdminResetPasswordController::class, 'post', 'as' => 'admin.requestResetPassword'])->name('requestResetPassword');
        });
    });
    
    Route::middleware(['auth:admin', 'is_admin'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index', 'as' => 'admin.dashboard'])->name('dashboard');

        Route::prefix('/profile')->group(function () {
            Route::get('/', [AdminAccountController::class, 'index', 'as' => 'admin.profile'])->name('profile');
            Route::post('/update', [AdminAccountController::class, 'update', 'as' => 'admin.profile_update'])->name('profile_update');
            Route::post('/profile-password-update', [AdminAccountController::class, 'profile_password', 'as' => 'admin.profile_password'])->name('profile_password_update');
        });

        Route::prefix('/enquiry')->group(function () {
            Route::get('/', [EnquiryPaginateController::class, 'index', 'as' => 'admin.enquiry.view'])->name('enquiry_view');
            Route::get('/view/{id}', [EnquiryViewController::class, 'index', 'as' => 'admin.enquiry.display'])->name('enquiry_display');
            Route::post('/reply/{id}', [EnquiryReplyController::class, 'index', 'as' => 'admin.enquiry.reply'])->name('enquiry_reply');
            Route::get('/excel', [EnquiryExportController::class, 'index', 'as' => 'admin.enquiry.excel'])->name('enquiry_excel');
            Route::get('/delete/{id}', [EnquiryDeleteController::class, 'index', 'as' => 'admin.enquiry.delete'])->name('enquiry_delete');
        });

        Route::prefix('/user')->group(function () {
            Route::get('/', [UserPaginateController::class, 'index', 'as' => 'admin.subadmin.view'])->name('subadmin_view');
            Route::get('/view/{id}', [UserViewController::class, 'index', 'as' => 'admin.subadmin.display'])->name('subadmin_display');
            Route::get('/email-test', [UserMailTestController::class, 'get', 'as' => 'admin.subadmin.email'])->name('subadmin_email');
            Route::get('/create', [UserCreateController::class, 'get', 'as' => 'admin.subadmin.create'])->name('subadmin_create');
            Route::post('/create', [UserCreateController::class, 'post', 'as' => 'admin.subadmin.store'])->name('subadmin_store');
            Route::post('/multi-delete', [UserMultiDeleteController::class, 'index', 'as' => 'admin.subadmin.multi_delete'])->name('subadmin_multi_delete');
            Route::post('/multi-status', [UserMultiStatusToggleController::class, 'index', 'as' => 'admin.subadmin.multi_status'])->name('subadmin_multi_status');
            Route::get('/excel', [UserExportController::class, 'index', 'as' => 'admin.subadmin.excel'])->name('subadmin_excel');
            Route::get('/edit/{id}', [UserUpdateController::class, 'get', 'as' => 'admin.subadmin.edit'])->name('subadmin_edit');
            Route::post('/edit/{id}', [UserUpdateController::class, 'post', 'as' => 'admin.subadmin.update'])->name('subadmin_update');
            Route::get('/delete/{id}', [UserDeleteController::class, 'index', 'as' => 'admin.subadmin.delete'])->name('subadmin_delete');
            Route::get('/make-previledge/{id}', [UserPreviledgeToggleController::class, 'index', 'as' => 'admin.subadmin.makeUserPreviledge'])->name('subadmin_makeUserPreviledge');
            Route::get('/toggle-status/{id}', [UserStatusToggleController::class, 'index', 'as' => 'admin.subadmin.toggleUserStatus'])->name('subadmin_toggleUserStatus');
        });
    
        Route::prefix('/image')->group(function () {
            Route::get('/', [ImageController::class, 'view', 'as' => 'admin.image.view'])->name('image_view');
            Route::get('/view/{id}', [ImageController::class, 'display', 'as' => 'admin.image.display'])->name('image_display');
            Route::get('/create', [ImageController::class, 'create', 'as' => 'admin.image.create'])->name('image_create');
            Route::post('/create', [ImageController::class, 'store', 'as' => 'admin.image.store'])->name('image_store');
            Route::get('/excel', [ImageController::class, 'excel', 'as' => 'admin.image.excel'])->name('image_excel');
            Route::post('/multi-status-toggle', [ImageController::class, 'multiStatusToggle', 'as' => 'admin.image.multi_status'])->name('image_multi_status');
            Route::post('/multi-restriction-toggle', [ImageController::class, 'multiRestrictionToggle', 'as' => 'admin.image.multi_restriction'])->name('image_multi_restriction');
            Route::post('/multi-delete', [ImageController::class, 'multiDelete', 'as' => 'admin.image.multi_delete'])->name('image_multi_delete');
            Route::get('/file/{uuid}', [ImageController::class, 'file', 'as' => 'admin.image.file'])->name('image_file');
            Route::get('/edit/{id}', [ImageController::class, 'edit', 'as' => 'admin.image.edit'])->name('image_edit');
            Route::post('/edit/{id}', [ImageController::class, 'update', 'as' => 'admin.image.update'])->name('image_update');
            Route::get('/delete/{id}', [ImageController::class, 'delete', 'as' => 'admin.image.delete'])->name('image_delete');
            Route::get('/bulk-upload', [ImageController::class, 'bulk_upload', 'as' => 'admin.image.bulk_upload'])->name('image_bulk_upload');
            Route::post('/bulk-upload', [ImageController::class, 'bulk_upload_store', 'as' => 'admin.image.bulk_upload_store'])->name('image_bulk_upload_store');
            Route::prefix('/trash')->group(function () {
                Route::get('/', [ImageTrashController::class, 'viewTrash', 'as' => 'admin.image.viewTrash'])->name('image_view_trash');
                Route::get('/restore/{id}', [ImageTrashController::class, 'restoreTrash', 'as' => 'admin.image.restoreTrash'])->name('image_restore_trash');
                Route::get('/restore-all', [ImageTrashController::class, 'restoreAllTrash', 'as' => 'admin.image.restoreAllTrash'])->name('image_restore_all_trash');
                Route::get('/view/{id}', [ImageTrashController::class, 'displayTrash', 'as' => 'admin.image.displayTrash'])->name('image_display_trash');
                Route::get('/delete/{id}', [ImageTrashController::class, 'deleteTrash', 'as' => 'admin.image.deleteTrash'])->name('image_delete_trash');
            });
    
        });
    
        Route::prefix('/document')->group(function () {
            Route::get('/', [DocumentController::class, 'view', 'as' => 'admin.document.view'])->name('document_view');
            Route::get('/view/{id}', [DocumentController::class, 'display', 'as' => 'admin.document.display'])->name('document_display');
            Route::get('/create', [DocumentController::class, 'create', 'as' => 'admin.document.create'])->name('document_create');
            Route::post('/create', [DocumentController::class, 'store', 'as' => 'admin.document.store'])->name('document_store');
            Route::get('/excel', [DocumentController::class, 'excel', 'as' => 'admin.document.excel'])->name('document_excel');
            Route::post('/multi-status-toggle', [DocumentController::class, 'multiStatusToggle', 'as' => 'admin.document.multi_status'])->name('document_multi_status');
            Route::post('/multi-restriction-toggle', [DocumentController::class, 'multiRestrictionToggle', 'as' => 'admin.document.multi_restriction'])->name('document_multi_restriction');
            Route::post('/multi-delete', [DocumentController::class, 'multiDelete', 'as' => 'admin.document.multi_delete'])->name('document_multi_delete');
            Route::get('/reader/{uuid}', [DocumentReaderController::class, 'index', 'as' => 'admin.document.reader'])->name('document_reader');
            Route::get('/file/{uuid}', [DocumentController::class, 'file', 'as' => 'admin.document.file'])->name('document_file');
            Route::get('/edit/{id}', [DocumentController::class, 'edit', 'as' => 'admin.document.edit'])->name('document_edit');
            Route::post('/edit/{id}', [DocumentController::class, 'update', 'as' => 'admin.document.update'])->name('document_update');
            Route::get('/delete/{id}', [DocumentController::class, 'delete', 'as' => 'admin.document.delete'])->name('document_delete');
            Route::get('/bulk-upload', [DocumentController::class, 'bulk_upload', 'as' => 'admin.document.bulk_upload'])->name('document_bulk_upload');
            Route::post('/bulk-upload', [DocumentController::class, 'bulk_upload_store', 'as' => 'admin.document.bulk_upload_store'])->name('document_bulk_upload_store');
            Route::prefix('/trash')->group(function () {
                Route::get('/', [DocumentTrashController::class, 'viewTrash', 'as' => 'admin.document.viewTrash'])->name('document_view_trash');
                Route::get('/restore/{id}', [DocumentTrashController::class, 'restoreTrash', 'as' => 'admin.document.restoreTrash'])->name('document_restore_trash');
                Route::get('/restore-all', [DocumentTrashController::class, 'restoreAllTrash', 'as' => 'admin.document.restoreAllTrash'])->name('document_restore_all_trash');
                Route::get('/view/{id}', [DocumentTrashController::class, 'displayTrash', 'as' => 'admin.document.displayTrash'])->name('document_display_trash');
                Route::get('/delete/{id}', [DocumentTrashController::class, 'deleteTrash', 'as' => 'admin.document.deleteTrash'])->name('document_delete_trash');
            });
        });
    
        Route::prefix('/audio')->group(function () {
            Route::get('/', [AudioController::class, 'view', 'as' => 'admin.audio.view'])->name('audio_view');
            Route::get('/view/{id}', [AudioController::class, 'display', 'as' => 'admin.audio.display'])->name('audio_display');
            Route::get('/create', [AudioController::class, 'create', 'as' => 'admin.audio.create'])->name('audio_create');
            Route::post('/create', [AudioController::class, 'store', 'as' => 'admin.audio.store'])->name('audio_store');
            Route::get('/excel', [AudioController::class, 'excel', 'as' => 'admin.audio.excel'])->name('audio_excel');
            Route::post('/multi-status-toggle', [AudioController::class, 'multiStatusToggle', 'as' => 'admin.audio.multi_status'])->name('audio_multi_status');
            Route::post('/multi-restriction-toggle', [AudioController::class, 'multiRestrictionToggle', 'as' => 'admin.audio.multi_restriction'])->name('audio_multi_restriction');
            Route::post('/multi-delete', [AudioController::class, 'multiDelete', 'as' => 'admin.audio.multi_delete'])->name('audio_multi_delete');
            Route::get('/file/{uuid}', [AudioController::class, 'file', 'as' => 'admin.audio.file'])->name('audio_file');
            Route::get('/edit/{id}', [AudioController::class, 'edit', 'as' => 'admin.audio.edit'])->name('audio_edit');
            Route::post('/edit/{id}', [AudioController::class, 'update', 'as' => 'admin.audio.update'])->name('audio_update');
            Route::get('/delete/{id}', [AudioController::class, 'delete', 'as' => 'admin.audio.delete'])->name('audio_delete');
            Route::get('/bulk-upload', [AudioController::class, 'bulk_upload', 'as' => 'admin.audio.bulk_upload'])->name('audio_bulk_upload');
            Route::post('/bulk-upload', [AudioController::class, 'bulk_upload_store', 'as' => 'admin.audio.bulk_upload_store'])->name('audio_bulk_upload_store');
            Route::prefix('/trash')->group(function () {
                Route::get('/', [AudioTrashController::class, 'viewTrash', 'as' => 'admin.audio.viewTrash'])->name('audio_view_trash');
                Route::get('/restore/{id}', [AudioTrashController::class, 'restoreTrash', 'as' => 'admin.audio.restoreTrash'])->name('audio_restore_trash');
                Route::get('/restore-all', [AudioTrashController::class, 'restoreAllTrash', 'as' => 'admin.audio.restoreAllTrash'])->name('audio_restore_all_trash');
                Route::get('/view/{id}', [AudioTrashController::class, 'displayTrash', 'as' => 'admin.audio.displayTrash'])->name('audio_display_trash');
                Route::get('/delete/{id}', [AudioTrashController::class, 'deleteTrash', 'as' => 'admin.audio.deleteTrash'])->name('audio_delete_trash');
            });
        });

        Route::prefix('/video')->group(function () {
            Route::get('/', [VideoController::class, 'view', 'as' => 'admin.video.view'])->name('video_view');
            Route::get('/view/{id}', [VideoController::class, 'display', 'as' => 'admin.video.display'])->name('video_display');
            Route::get('/create', [VideoController::class, 'create', 'as' => 'admin.video.create'])->name('video_create');
            Route::post('/create', [VideoController::class, 'store', 'as' => 'admin.video.store'])->name('video_store');
            Route::get('/excel', [VideoController::class, 'excel', 'as' => 'admin.video.excel'])->name('video_excel');
            Route::post('/multi-status-toggle', [VideoController::class, 'multiStatusToggle', 'as' => 'admin.video.multi_status'])->name('video_multi_status');
            Route::post('/multi-restriction-toggle', [VideoController::class, 'multiRestrictionToggle', 'as' => 'admin.video.multi_restriction'])->name('video_multi_restriction');
            Route::post('/multi-delete', [VideoController::class, 'multiDelete', 'as' => 'admin.video.multi_delete'])->name('video_multi_delete');
            Route::get('/edit/{id}', [VideoController::class, 'edit', 'as' => 'admin.video.edit'])->name('video_edit');
            Route::post('/edit/{id}', [VideoController::class, 'update', 'as' => 'admin.video.update'])->name('video_update');
            Route::get('/delete/{id}', [VideoController::class, 'delete', 'as' => 'admin.video.delete'])->name('video_delete');
            Route::get('/bulk-upload', [VideoController::class, 'bulk_upload', 'as' => 'admin.video.bulk_upload'])->name('video_bulk_upload');
            Route::post('/bulk-upload', [VideoController::class, 'bulk_upload_store', 'as' => 'admin.video.bulk_upload_store'])->name('video_bulk_upload_store');
            Route::prefix('/trash')->group(function () {
                Route::get('/', [VideoTrashController::class, 'viewTrash', 'as' => 'admin.video.viewTrash'])->name('video_view_trash');
                Route::get('/restore/{id}', [VideoTrashController::class, 'restoreTrash', 'as' => 'admin.video.restoreTrash'])->name('video_restore_trash');
                Route::get('/restore-all', [VideoTrashController::class, 'restoreAllTrash', 'as' => 'admin.video.restoreAllTrash'])->name('video_restore_all_trash');
                Route::get('/view/{id}', [VideoTrashController::class, 'displayTrash', 'as' => 'admin.video.displayTrash'])->name('video_display_trash');
                Route::get('/delete/{id}', [VideoTrashController::class, 'deleteTrash', 'as' => 'admin.video.deleteTrash'])->name('video_delete_trash');
            });
        });

        Route::prefix('/access-request')->group(function () {
            Route::prefix('/image')->group(function () {
                Route::get('/', [ImageAccessController::class, 'viewaccess', 'as' => 'admin.image.viewaccess'])->name('image_view_access');
                Route::get('/display/{id}', [ImageAccessController::class, 'displayAccess', 'as' => 'admin.image.displayAccess'])->name('image_display_access');
                Route::get('/delete/{id}', [ImageAccessController::class, 'deleteAccess', 'as' => 'admin.image.deleteAccess'])->name('image_delete_access');
                Route::post('/toggle/{id}', [ImageAccessController::class, 'toggleAccess', 'as' => 'admin.image.toggleAccess'])->name('image_toggle_access');
            });
            Route::prefix('/document')->group(function () {
                Route::get('/', [DocumentAccessController::class, 'viewaccess', 'as' => 'admin.document.viewaccess'])->name('document_view_access');
                Route::get('/display/{id}', [DocumentAccessController::class, 'displayAccess', 'as' => 'admin.document.displayAccess'])->name('document_display_access');
                Route::get('/delete/{id}', [DocumentAccessController::class, 'deleteAccess', 'as' => 'admin.document.deleteAccess'])->name('document_delete_access');
                Route::post('/toggle/{id}', [DocumentAccessController::class, 'toggleAccess', 'as' => 'admin.document.toggleAccess'])->name('document_toggle_access');
            });
            Route::prefix('/audio')->group(function () {
                Route::get('/', [AudioAccessController::class, 'viewaccess', 'as' => 'admin.audio.viewaccess'])->name('audio_view_access');
                Route::get('/display/{id}', [AudioAccessController::class, 'displayAccess', 'as' => 'admin.audio.displayAccess'])->name('audio_display_access');
                Route::get('/delete/{id}', [AudioAccessController::class, 'deleteAccess', 'as' => 'admin.audio.deleteAccess'])->name('audio_delete_access');
                Route::post('/toggle/{id}', [AudioAccessController::class, 'toggleAccess', 'as' => 'admin.audio.toggleAccess'])->name('audio_toggle_access');
            });
            Route::prefix('/video')->group(function () {
                Route::get('/', [VideoAccessController::class, 'viewaccess', 'as' => 'admin.video.viewaccess'])->name('video_view_access');
                Route::get('/display/{id}', [VideoAccessController::class, 'displayAccess', 'as' => 'admin.video.displayAccess'])->name('video_display_access');
                Route::get('/delete/{id}', [VideoAccessController::class, 'deleteAccess', 'as' => 'admin.video.deleteAccess'])->name('video_delete_access');
                Route::post('/toggle/{id}', [VideoAccessController::class, 'toggleAccess', 'as' => 'admin.video.toggleAccess'])->name('video_toggle_access');
            });
        });
    
        Route::prefix('/report')->group(function () {
            Route::prefix('/image')->group(function () {
                Route::get('/', [ImageReportController::class, 'viewreport', 'as' => 'admin.image.viewreport'])->name('image_view_report');
                Route::get('/display/{id}', [ImageReportController::class, 'displayReport', 'as' => 'admin.image.displayReport'])->name('image_display_report');
                Route::get('/delete/{id}', [ImageReportController::class, 'deleteReport', 'as' => 'admin.image.deleteReport'])->name('image_delete_report');
                Route::post('/toggle/{id}', [ImageReportController::class, 'toggleReport', 'as' => 'admin.image.toggleReport'])->name('image_toggle_report');
            });
            Route::prefix('/document')->group(function () {
                Route::get('/', [DocumentReportController::class, 'viewreport', 'as' => 'admin.document.viewreport'])->name('document_view_report');
                Route::get('/display/{id}', [DocumentReportController::class, 'displayReport', 'as' => 'admin.document.displayReport'])->name('document_display_report');
                Route::get('/delete/{id}', [DocumentReportController::class, 'deleteReport', 'as' => 'admin.document.deleteReport'])->name('document_delete_report');
                Route::post('/toggle/{id}', [DocumentReportController::class, 'toggleReport', 'as' => 'admin.document.toggleReport'])->name('document_toggle_report');
            });
            Route::prefix('/audio')->group(function () {
                Route::get('/', [AudioReportController::class, 'viewreport', 'as' => 'admin.audio.viewreport'])->name('audio_view_report');
                Route::get('/display/{id}', [AudioReportController::class, 'displayReport', 'as' => 'admin.audio.displayReport'])->name('audio_display_report');
                Route::get('/delete/{id}', [AudioReportController::class, 'deleteReport', 'as' => 'admin.audio.deleteReport'])->name('audio_delete_report');
                Route::post('/toggle/{id}', [AudioReportController::class, 'toggleReport', 'as' => 'admin.audio.toggleReport'])->name('audio_toggle_report');
            });
            Route::prefix('/video')->group(function () {
                Route::get('/', [VideoReportController::class, 'viewreport', 'as' => 'admin.video.viewreport'])->name('video_view_report');
                Route::get('/display/{id}', [VideoReportController::class, 'displayReport', 'as' => 'admin.video.displayReport'])->name('video_display_report');
                Route::get('/delete/{id}', [VideoReportController::class, 'deleteReport', 'as' => 'admin.video.deleteReport'])->name('video_delete_report');
                Route::post('/toggle/{id}', [VideoReportController::class, 'toggleReport', 'as' => 'admin.video.toggleReport'])->name('video_toggle_report');
            });
        });
    
        Route::prefix('/page')->group(function () {
            Route::get('/home', [PageController::class, 'home_page', 'as' => 'admin.page.home_page'])->name('home_page');
            Route::get('/about', [PageController::class, 'about_page', 'as' => 'admin.page.about_page'])->name('about_page');
            Route::post('/store-page', [PageController::class, 'storePage', 'as' => 'admin.page.storePage'])->name('storePage');
            Route::post('/update-page/{id}', [PageController::class, 'updatePage', 'as' => 'admin.page.updatePage'])->name('updatePage');
            Route::post('/store-page-content', [PageController::class, 'storePageContent', 'as' => 'admin.page.storePageContent'])->name('storePageContent');
            Route::post('/update-page-content', [PageController::class, 'updatePageContent', 'as' => 'admin.page.updatePageContent'])->name('updatePageContent');
            Route::get('/delete-page-content/{id}', [PageController::class, 'deletePageContent', 'as' => 'admin.page.deletePageContent'])->name('deletePageContent');
            Route::post('/get-page-content', [PageController::class, 'getPageContent', 'as' => 'admin.page.getPageContent'])->name('getPageContent');
            Route::prefix('/dynamic')->group(function () {
                Route::get('/', [PageController::class, 'dynamic_page_list', 'as' => 'admin.page.dynamic_page_list'])->name('dynamic_page_list');
                Route::get('/edit/{id}', [PageController::class, 'edit_dynamic_page', 'as' => 'admin.page.edit_dynamic_page'])->name('edit_dynamic_page');
                Route::get('/delete/{id}', [PageController::class, 'deletePage', 'as' => 'admin.page.deletePage'])->name('deletePage');
            });
    
        });
    
        Route::prefix('/banner')->group(function () {
            Route::get('/', [BannerPaginateController::class, 'index', 'as' => 'admin.page.banner'])->name('banner_view');
            Route::post('/store', [BannerCreateController::class, 'post', 'as' => 'admin.page.storeBanner'])->name('banner_store');
            Route::get('/delete/{id}', [BannerDeleteController::class, 'index', 'as' => 'admin.page.deleteBanner'])->name('banner_delete');
            Route::prefix('/quote')->group(function () {
                Route::get('/', [BannerQuotePaginateController::class, 'index', 'as' => 'admin.page.banner_quote'])->name('banner_quote_view');
                Route::post('/store', [BannerQuoteCreateController::class, 'post', 'as' => 'admin.page.storeBannerQuote'])->name('banner_quote_store');
                Route::get('/delete/{id}', [BannerQuoteDeleteController::class, 'index', 'as' => 'admin.page.deleteBannerQuote'])->name('banner_quote_delete');
            });
        });
    
        Route::prefix('/language')->group(function () {
            Route::get('/', [LanguagePaginateController::class, 'index', 'as' => 'admin.language.view'])->name('language_view');
            Route::get('/view/{id}', [LanguageViewController::class, 'index', 'as' => 'admin.language.display'])->name('language_display');
            Route::get('/create', [LanguageCreateController::class, 'get', 'as' => 'admin.language.create'])->name('language_create');
            Route::post('/create', [LanguageCreateController::class, 'post', 'as' => 'admin.language.store'])->name('language_store');
            Route::get('/excel', [LanguageExportController::class, 'index', 'as' => 'admin.language.excel'])->name('language_excel');
            Route::get('/edit/{id}', [LanguageUpdateController::class, 'get', 'as' => 'admin.language.edit'])->name('language_edit');
            Route::post('/edit/{id}', [LanguageUpdateController::class, 'post', 'as' => 'admin.language.update'])->name('language_update');
            Route::get('/delete/{id}', [LanguageDeleteController::class, 'index', 'as' => 'admin.language.delete'])->name('language_delete');
        });
    
        Route::prefix('/faq')->group(function () {
            Route::get('/', [FAQPaginateController::class, 'index', 'as' => 'admin.faq.view'])->name('faq_view');
            Route::post('/create', [FAQCreateController::class, 'post', 'as' => 'admin.faq.store'])->name('faq_store');
            Route::post('/edit', [FAQUpdateController::class, 'post', 'as' => 'admin.faq.update'])->name('faq_update');
            Route::get('/delete/{id}', [FAQDeleteController::class, 'index', 'as' => 'admin.faq.delete'])->name('faq_delete');
        });

        Route::get('logs', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index']);

        Route::get('/logout', [AdminLogoutController::class, 'get', 'as' => 'admin.logout'])->name('logout');
    });
});
