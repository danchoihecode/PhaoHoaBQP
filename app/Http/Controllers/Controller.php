<?php

namespace App\Http\Controllers;

use App\Main\Config\AppConst;
use Closure;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

use function App\Main\Helpers\responseJsonFail;
use function App\Main\Helpers\responseJsonSuccess;



class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Display a listing of the resource.
     *
     * @param  \Closure  $closure
     * @param  string|null $messageSuccess
     * @param  string|null $messageError
     * @param  string|null  ...$params
     * @return \App\AppMain\Helpers\responseJsonSuccess|\App\AppMain\Helpers\responseJsonFail
     */
    protected function baseAction(Closure $closure, $messageSuccess = 'Success', $messageError = 'Error', ...$params)
    {
        try {
            $result = $closure();
        } catch (Throwable $e) {
            Log::error($e->getMessage());
            Log::error($e->getTraceAsString());
            return responseJsonFail(AppConst::CODE_EXCEPTION_MESSAGE == $e->getCode() ? $e->getMessage() :  __($messageError));
        }

        return ($result || is_array($result)) ? responseJsonSuccess($result, __($messageSuccess)) : responseJsonFail(__($messageError));
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Closure  $closure
     * @param  string|null $messageSuccess
     * @param  string|null $messageError
     * @param  string|null  ...$params
     * @return \App\AppMain\Helpers\responseJsonSuccess|\App\AppMain\Helpers\responseJsonFail
     */
    protected function baseActionTransaction(Closure $closure, $messageSuccess = 'Success', $messageError = 'Error', ...$params)
    {
        DB::beginTransaction();
        try {
            $result = $closure();
            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            Log::warning($e->getMessage());
            return responseJsonFail(AppConst::CODE_EXCEPTION_MESSAGE == $e->getCode() ? $e->getMessage() :  __($messageError));
        }

        return $result ? responseJsonSuccess($result, __($messageSuccess)) : responseJsonFail(__($messageError));
    }


    // Thêm tham số $productService vào hàm
    protected function productCreationTransaction($productService, Closure $closure, $messageSuccess = 'Success', $messageError = 'Error')
    {
        $uploadedImages = [];  // Khởi tạo mảng để lưu trữ các ảnh đã tải lên
        DB::beginTransaction();
        try {
            // Truyền mảng $uploadedImages vào closure như một tham chiếu
            $result = $closure($uploadedImages, $productService);
            DB::commit();
            return $result ? responseJsonSuccess($result, __($messageSuccess)) : responseJsonFail(__($messageError));
        } catch (Throwable $e) {
            DB::rollBack();
            // Xóa tất cả ảnh đã upload nếu có lỗi
            foreach ($uploadedImages as $imageUrl) {
                $productService->deleteImage($imageUrl);
            }
            Log::warning($e->getMessage());
            return responseJsonFail($e->getMessage());
        }
    }



    // Thêm tham số $productService vào hàm
    protected function productUpdateTransaction($productService, Closure $closure, $messageSuccess = 'Success', $messageError = 'Error')
    {
        $uploadedImages = [];  // Khởi tạo mảng để lưu trữ các ảnh đã tải lên
        DB::beginTransaction();
        try {
            // Truyền mảng $uploadedImages vào closure như một tham chiếu
            $result = $closure($uploadedImages, $productService);
            DB::commit();
            return $result ? responseJsonSuccess($result, __($messageSuccess)) : responseJsonFail(__($messageError));
        } catch (Throwable $e) {
            DB::rollBack();
            // Xóa tất cả ảnh đã upload nếu có lỗi
            foreach ($uploadedImages as $imageUrl) {
                $productService->deleteImage($imageUrl);
            }
            Log::warning($e->getMessage());
            return responseJsonFail($e->getMessage());
        }
    }
}
