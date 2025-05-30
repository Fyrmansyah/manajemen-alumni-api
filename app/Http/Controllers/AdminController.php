<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseBuilder;
use App\Http\Requests\CreateAdminRequest;
use App\Http\Requests\UpdateAdminRequest;
use App\Models\Admin;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $data = Admin::all();

        return ResponseBuilder::success()
            ->data($data)
            ->build();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateAdminRequest $request): JsonResponse
    {
        $admin = Admin::create($request->validated());
        return ResponseBuilder::success()
            ->data($admin)
            ->message('sukses membuat data admin baru')
            ->build();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $admin = Admin::query()->find($id);
        if (!$admin) {
            return ResponseBuilder::fail()
                ->message('data admin tidak ditemukan')
                ->httpCode(Response::HTTP_NOT_FOUND)
                ->build();
        }
        return ResponseBuilder::success()->data($admin)->build();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAdminRequest $request, string $id): JsonResponse
    {
        $admin = Admin::query()->find($id);
        if (!$admin) {
            return ResponseBuilder::fail()
                ->message('data admin tidak ditemukan')
                ->build();
        }

        $admin->update($request->validated());

        return ResponseBuilder::success()
            ->message('sukses memperbarui data admin')
            ->build();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $admin = Admin::query()->find($id);

        if (!$admin) {
            return ResponseBuilder::fail()
                ->message('data admin tidak ditemukan')
                ->build();
        }

        $admin->delete();

        return ResponseBuilder::success()
            ->message('data admin sukses dihapus')
            ->build();
    }
}
