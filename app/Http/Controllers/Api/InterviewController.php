<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Interview;
use App\Models\InterviewDetail;
use App\Models\Application;
use App\Http\Requests\StoreInterviewRequest;
use App\Http\Requests\UpdateInterviewRequest;
use App\Http\Resources\Api\InterviewResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class InterviewController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Interview::class, 'interview');
    }

    public function index(): AnonymousResourceCollection
    {
        // ✅ تم التصحيح: applicant بدلاً من appliable
        $interviews = Interview::with(['jobRequest.department', 'details.application.applicant'])
            ->orderBy('InterviewDate', 'desc')
            ->paginate(15);

        return InterviewResource::collection($interviews);
    }

    public function store(StoreInterviewRequest $request): InterviewResource
    {
        $interview = DB::transaction(function () use ($request) {
            $interview = Interview::create([
                'job_request_id' => $request->job_request_id,
                'EmpCode'        => $request->EmpCode,
                'InterviewDate'  => $request->InterviewDate,
                'Location'       => $request->Location,
                'Status'         => $request->Status ?? 'Scheduled',
                'Notes'          => $request->Notes,
            ]);

            $detailsData = [];
            $applicationIds = [];

            foreach ($request->candidates as $candidate) {
                $detailsData[] = new InterviewDetail([
                    'application_id' => $candidate['application_id'],
                    'InterviewTime'  => $candidate['InterviewTime'],
                ]);
                $applicationIds[] = $candidate['application_id'];
            }

            $interview->details()->saveMany($detailsData);

            Application::whereIn('id', $applicationIds)->update(['ApplicationStatus' => 'Interview']);

            return $interview;
        });

        // ✅ تم التصحيح: applicant بدلاً من appliable
        $interview->load(['jobRequest', 'details.application.applicant']);

        return new InterviewResource($interview);
    }

   public function show(Interview $interview): InterviewResource
{
    // ✅ التعديل: أضفنا 'applicant.documents' لعلاقة الـ load
    $interview->load([
        'jobRequest.department',
        'details.application.applicant.documents' // جلب مستندات المتقدم
    ]);

    return new InterviewResource($interview);
}

    public function update(UpdateInterviewRequest $request, Interview $interview): InterviewResource
    {
        DB::transaction(function () use ($request, $interview) {
            $interview->update($request->safe()->except('candidates'));

            if ($request->has('candidates')) {
                foreach ($request->candidates as $candidate) {
                    InterviewDetail::updateOrCreate(
                        [
                            'interview_id'   => $interview->id,
                            'application_id' => $candidate['application_id']
                        ],
                        [
                            'InterviewTime'   => $candidate['InterviewTime'] ?? DB::raw('InterviewTime'),
                            'EvaluationScore' => $candidate['EvaluationScore'] ?? null,
                            'Result'          => $candidate['Result'] ?? 'Pending',
                            'Notes'           => $candidate['Notes'] ?? null,
                        ]
                    );
                }
            }
        });

        // ✅ تم التصحيح: applicant بدلاً من appliable
        $interview->load(['jobRequest', 'details.application.applicant']);

        return new InterviewResource($interview);
    }

    public function destroy(Interview $interview): JsonResponse
    {
        $interview->delete();

        return response()->json([
            'status' => true,
            'message' => 'تم إلغاء جلسة المقابلات بنجاح'
        ], 200);
    }

    public function getByApplication($applicationId): AnonymousResourceCollection
    {
        $interviews = Interview::whereHas('details', function ($query) use ($applicationId) {
            $query->where('application_id', $applicationId);
        })
        ->with(['jobRequest', 'details' => function($query) use ($applicationId) {
            // ✅ تم التصحيح: applicant بدلاً من appliable
            $query->where('application_id', $applicationId)->with('application.applicant');
        }])
        ->orderBy('InterviewDate', 'asc')
        ->get();

        return InterviewResource::collection($interviews);
    }

    public function getByInterviewer($empCode): AnonymousResourceCollection
    {
        $interviews = Interview::where('EmpCode', $empCode)
            // ✅ تم التصحيح: applicant بدلاً من appliable
            ->with(['jobRequest.department', 'details.application.applicant'])
            ->orderBy('InterviewDate', 'asc')
            ->get();

        return InterviewResource::collection($interviews);
    }
}
