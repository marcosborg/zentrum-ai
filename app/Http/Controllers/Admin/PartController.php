<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\MassDestroyPartRequest;
use App\Http\Requests\StorePartRequest;
use App\Http\Requests\UpdatePartRequest;
use App\Models\Part;
use Gate;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\Response;

class PartController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {
        abort_if(Gate::denies('part_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $parts = Part::with(['media'])->get();

        return view('admin.parts.index', compact('parts'));
    }

    public function create()
    {
        abort_if(Gate::denies('part_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.parts.create');
    }

    public function store(StorePartRequest $request)
    {
        $part = Part::create($request->all());

        if ($request->input('photo', false)) {
            $part->addMedia(storage_path('tmp/uploads/' . basename($request->input('photo'))))->toMediaCollection('photo');
        }

        if ($media = $request->input('ck-media', false)) {
            Media::whereIn('id', $media)->update(['model_id' => $part->id]);
        }

        return redirect()->route('admin.parts.index');
    }

    public function edit(Part $part)
    {
        abort_if(Gate::denies('part_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.parts.edit', compact('part'));
    }

    public function update(UpdatePartRequest $request, Part $part)
    {
        $part->update($request->all());

        if ($request->input('photo', false)) {
            if (! $part->photo || $request->input('photo') !== $part->photo->file_name) {
                if ($part->photo) {
                    $part->photo->delete();
                }
                $part->addMedia(storage_path('tmp/uploads/' . basename($request->input('photo'))))->toMediaCollection('photo');
            }
        } elseif ($part->photo) {
            $part->photo->delete();
        }

        return redirect()->route('admin.parts.index');
    }

    public function show(Part $part)
    {
        abort_if(Gate::denies('part_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.parts.show', compact('part'));
    }

    public function destroy(Part $part)
    {
        abort_if(Gate::denies('part_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $part->delete();

        return back();
    }

    public function massDestroy(MassDestroyPartRequest $request)
    {
        $parts = Part::find(request('ids'));

        foreach ($parts as $part) {
            $part->delete();
        }

        return response(null, Response::HTTP_NO_CONTENT);
    }

    public function storeCKEditorImages(Request $request)
    {
        abort_if(Gate::denies('part_create') && Gate::denies('part_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $model         = new Part();
        $model->id     = $request->input('crud_id', 0);
        $model->exists = true;
        $media         = $model->addMediaFromRequest('upload')->toMediaCollection('ck-media');

        return response()->json(['id' => $media->id, 'url' => $media->getUrl()], Response::HTTP_CREATED);
    }
}
