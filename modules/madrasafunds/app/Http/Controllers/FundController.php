<?php

declare(strict_types=1);

namespace Modules\MadrasaFunds\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\MadrasaFunds\Enums\FundType;
use Modules\MadrasaFunds\Http\Requests\StoreFundRequest;
use Modules\MadrasaFunds\Http\Requests\UpdateFundRequest;
use Modules\MadrasaFunds\Models\Fund;
use Modules\MadrasaFunds\Services\FundService;

class FundController extends MadrasaFundsController
{
    public function __construct(
        private readonly FundService $fundService
    ) {
    }

    public function index(): Renderable
    {
        abort_unless(auth()->user()->can('madrasa_funds.manage_funds'), 403);

        $this->setBreadcrumbTitle(mf_bi('Funds'))
            ->setBreadcrumbIcon('lucide:wallet')
            ->setBreadcrumbActionButton(
                route('admin.madrasafunds.funds.create'),
                mf_bi('New Fund'),
                'lucide:plus',
                'madrasa_funds.manage_funds'
            );

        return $this->renderViewWithBreadcrumbs('madrasafunds::pages.funds.index');
    }

    public function data(Request $request): JsonResponse
    {
        abort_unless(auth()->user()->can('madrasa_funds.manage_funds'), 403);

        $query = Fund::query();

        if ($search = $request->string('search')->trim()->value()) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('name_urdu', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->string('type')->value());
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->string('status')->value() === 'active');
        }

        $sortable = ['id', 'name', 'type', 'created_at'];
        $sort = in_array($request->string('sort')->value(), $sortable, true) ? $request->string('sort')->value() : 'id';
        $dir = $request->string('direction')->value() === 'desc' ? 'desc' : 'asc';

        $paginated = $query->orderBy($sort, $dir)->paginate($request->integer('per_page', 15));

        $data = $paginated->getCollection()->map(fn (Fund $f) => [
            'id' => $f->id,
            'name' => $f->name,
            'name_urdu' => $f->name_urdu,
            'type' => $f->type instanceof FundType ? $f->type->label() : (string) $f->type,
            'status' => $f->is_active ? 'active' : 'inactive',
        ]);

        return response()->json([
            'data' => $data,
            'meta' => [
                'total' => $paginated->total(),
                'per_page' => $paginated->perPage(),
                'current_page' => $paginated->currentPage(),
                'last_page' => $paginated->lastPage(),
            ],
        ]);
    }

    public function create(): Renderable
    {
        abort_unless(auth()->user()->can('madrasa_funds.manage_funds'), 403);

        $this->setBreadcrumbTitle(mf_bi('New Fund'))
            ->setBreadcrumbIcon('lucide:wallet')
            ->addBreadcrumbItem(mf_bi('Funds'), route('admin.madrasafunds.funds.index'));

        return $this->renderViewWithBreadcrumbs('madrasafunds::pages.funds.create', [
            'fund' => null,
        ]);
    }

    public function store(StoreFundRequest $request): RedirectResponse
    {
        $this->fundService->create($request->validated());

        session()->flash('success', mf_bi('Fund created successfully.'));

        return redirect()->route('admin.madrasafunds.funds.index');
    }

    public function edit(Fund $fund): Renderable
    {
        abort_unless(auth()->user()->can('madrasa_funds.manage_funds'), 403);

        $this->setBreadcrumbTitle(mf_bi('Edit Fund'))
            ->setBreadcrumbIcon('lucide:wallet')
            ->addBreadcrumbItem(mf_bi('Funds'), route('admin.madrasafunds.funds.index'));

        return $this->renderViewWithBreadcrumbs('madrasafunds::pages.funds.edit', [
            'fund' => $fund,
        ]);
    }

    public function update(UpdateFundRequest $request, Fund $fund): RedirectResponse
    {
        $this->fundService->update($fund->id, $request->validated());

        session()->flash('success', mf_bi('Fund updated successfully.'));

        return redirect()->route('admin.madrasafunds.funds.index');
    }

    public function destroy(Fund $fund): RedirectResponse
    {
        abort_unless(auth()->user()->can('madrasa_funds.manage_funds'), 403);

        $this->fundService->delete($fund->id);

        session()->flash('success', mf_bi('Fund deleted successfully.'));

        return redirect()->route('admin.madrasafunds.funds.index');
    }
}
