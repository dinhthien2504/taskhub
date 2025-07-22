<?php
namespace App\Http\Controllers\EmailTemplates;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmailTemplates\StoreEmailTemplateRequest;
use App\Http\Requests\EmailTemplates\UpdateEmailTemplateRequest;
use App\Http\Resources\EmailTemplates\EmailTemplateResource;
use App\Services\EmailTemplates\EmailTemplateService;
use Illuminate\Http\Request;

class EmailTemplateController extends Controller
{
    protected $service;

    public function __construct(EmailTemplateService $service)
    {
        $this->service = $service;
    }


    public function index(Request $request)
    {
        try {
            $templates = $this->service->getAll($request);
            return EmailTemplateResource::collection($templates);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Lấy dữ liệu thất bại.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(StoreEmailTemplateRequest $request)
    {
        try {
            $template = $this->service->create($request->validated());
            return new EmailTemplateResource($template);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Thêm email temaplte thất bại.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function update(UpdateEmailTemplateRequest $request, $id)
    {
        try {
            $data = $request->validated();
            $template = $this->service->update($id, $data);
            return new EmailTemplateResource($template);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Xóa email tempalte thất bại.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function destroy($id)
    {
        try {
            $this->service->delete($id);
            return response()->json(['message' => 'Xóa email tempalte thành công.'], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Xóa email tempalte thất bại.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getTrashedTemplates()
    {
        try {
            $deletedTemplates = $this->service->getDeletedTemplates();
            return EmailTemplateResource::collection($deletedTemplates)->resolve();
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Lấy danh sách template đã xóa thất bại.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function restoreTemplate($id)
    {
        try {
            $this->service->restore($id);
            return response()->json([
                'message' => 'Template đã được khôi phục thành công.'
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Khôi phục template thất bại.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
