<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait FileUploadTrait
{
    /**
     * دالة لرفع ملف.
     * @param Request $request - الطلب القادم.
     * @param string $fieldName - اسم حقل الملف (e.g., 'identity_image').
     * @param string $folderName - اسم المجلد للحفظ (e.g., 'identities').
     * @return string|null - يرجع مسار الملف أو null.
     */
    public function uploadFile(Request $request, string $fieldName, string $folderName): ?string
    {
        if ($request->hasFile($fieldName)) {
            $path = $request->file($fieldName)->store($folderName, 'public');
            return $path;
        }

        return null;
    }
}
