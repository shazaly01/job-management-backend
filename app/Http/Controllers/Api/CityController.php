<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Http\Requests\StoreCityRequest;
use App\Http\Requests\UpdateCityRequest;
use App\Http\Resources\Api\CityResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\JsonResponse;

class CityController extends Controller
{
    /**
     * تأمين المتحكم وتطبيق الصلاحيات (Policies) تلقائياً.
     * سيعتمد Laravel على CityPolicy للتحقق من كل عملية.
     */
    public function __construct()
    {
        $this->authorizeResource(City::class, 'city');
    }

    /**
     * عرض قائمة المدن (مفيدة لملء القوائم المنسدلة في واجهة التسجيل).
     * يتم إرجاع المدن النشطة أولاً.
     */
    public function index(): AnonymousResourceCollection
    {
        $cities = City::orderBy('Name', 'asc')->paginate(20);
        return CityResource::collection($cities);
    }

    /**
     * إضافة مدينة جديدة إلى النظام.
     */
    public function store(StoreCityRequest $request): CityResource
    {
        $city = City::create($request->validated());

        return new CityResource($city);
    }

    /**
     * عرض بيانات مدينة محددة.
     */
    public function show(City $city): CityResource
    {
        return new CityResource($city);
    }

    /**
     * تحديث بيانات مدينة موجودة (مثل تغيير الاسم أو الحالة).
     */
    public function update(UpdateCityRequest $request, City $city): CityResource
    {
        $city->update($request->validated());

        return new CityResource($city);
    }

    /**
     * حذف مدينة (الحذف المرن Soft Delete).
     * ملاحظة: الحذف سيؤدي لنقلها للسلة ولن تظهر في القوائم.
     */
    public function destroy(City $city): JsonResponse
    {
        $city->delete();

        return response()->json([
            'status' => true,
            'message' => 'تم حذف المدينة بنجاح'
        ], 200);
    }

    /**
     * دالة إضافية: جلب المدن النشطة فقط (للمتقدمين الخارجيين).
     */
    public function getActiveCities(): AnonymousResourceCollection
    {
        $cities = City::where('IsActive', true)->orderBy('Name', 'asc')->get();
        return CityResource::collection($cities);
    }
}
