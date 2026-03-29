<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Http\Requests\StoreDocumentRequest; // المسار الصحيح 100%
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;

class DocumentController extends Controller
{
   /**
 * عرض قائمة المستندات (مع الفلترة حسب المتقدم إذا لزم الأمر).
 */
public function index(Request $request): JsonResponse
{
    // 1. التحقق من الصلاحية
    abort_if(!$request->user()->can('document.view'), 403, 'Unauthorized');

    // 2. نبدأ الاستعلام
    $query = Document::query();

    // 3. ✅ إضافة الفلترة الذكية
    // إذا أرسل الفرونت-أند documentable_id، نقوم بحصر النتائج له فقط
    if ($request->has('documentable_id')) {
        $query->where('documentable_id', $request->documentable_id)
              ->where('documentable_type', $request->documentable_type ?? 'App\\Models\\Applicant');
    }

    // 4. جلب البيانات (يمكنك إضافة with('documentable') إذا كنت تحتاج بيانات المتقدم مع الملف)
    $documents = $query->latest()->get();

    return response()->json([
        'data' => $documents
    ], 200);
}
    /**
     * رفع مستند جديد (مثل السيرة الذاتية) وربطه بالكيان.
     */
   public function store(StoreDocumentRequest $request): JsonResponse
{
    // السماح بالرفع إذا كان الشخص يملك صلاحية أو إذا كان المستهدف "متقدم" (بوابة خارجية)
    $isPublicApplicant = $request->documentable_type === 'App\\Models\\Applicant';

    if (!$isPublicApplicant) {
        abort_if(!$request->user() || !$request->user()->can('document.create'), 403, 'Unauthorized');
    }

    if ($request->hasFile('file')) {
        $file = $request->file('file');
        $path = $file->store('documents', 'local');

        $document = Document::create([
            'documentable_id'   => $request->documentable_id,
            'documentable_type' => $request->documentable_type,
            'name'              => $request->name ?? $file->getClientOriginalName(),
            'file_path'         => $path,
            'DocumentType'      => $request->DocumentType,
        ]);

        return response()->json(['message' => 'تم الرفع بنجاح', 'data' => $document], 201);
    }

    return response()->json(['message' => 'لم يتم إرفاق ملف'], 400);
}

    /**
     * عرض تفاصيل مستند معين.
     */
    public function show(Request $request, Document $document): JsonResponse
    {
        abort_if(!$request->user()->can('document.view'), 403, 'Unauthorized');

        $document->load('documentable');

        return response()->json([
            'data' => $document
        ], 200);
    }

    /**
     * حذف المستند (حذف مرن).
     */
    public function destroy(Request $request, Document $document): JsonResponse
    {
        // التحقق من صلاحية الحذف
        abort_if(!$request->user()->can('document.delete'), 403, 'Unauthorized');

        // الحذف المرن من قاعدة البيانات
        $document->delete();

        // إرجاع 204 No Content وهو الكود القياسي لحذف الموارد (وما ينتظره الاختبار)
        return response()->json(null, 204);
    }
}
