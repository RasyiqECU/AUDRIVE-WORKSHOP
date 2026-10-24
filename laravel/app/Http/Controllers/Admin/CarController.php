<?php


declare(strict_types=1);


namespace App\Http\Controllers\Admin;


use App\Enums\CarUnitStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCarUnitRequest;
use App\Http\Requests\Admin\UpdateCarUnitRequest;
use App\Models\CarPhoto;
use App\Models\CarUnit;
use App\Services\CarUnitService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;


class CarController extends Controller
{
    public function __construct(
        protected CarUnitService $carUnitService
    ) {}


    public function index(Request $request): View
    {
        $cars = CarUnit::query()
            ->when($request->status, fn($q, $status) => $q->where('status', $status))
            ->when($request->search, fn($q, $search) => $q->search($search))
            ->with('primaryPhoto')
            ->latest()
            ->paginate(15);


        return view('admin.cars.index', compact('cars'));
    }


    public function create(): View
    {
        return view('admin.cars.create');
    }


    public function store(StoreCarUnitRequest $request): RedirectResponse
    {
        $carUnit = $this->carUnitService->create($request->validated());


        // Upload photos jika ada
        if ($request->hasFile('photos')) {
            $this->carUnitService->uploadPhotos($carUnit, $request->file('photos'));
        }


        return redirect()
            ->route('admin.cars.show', $carUnit)
            ->with('success', 'Unit mobil berhasil ditambahkan.');
    }


    public function show(CarUnit $car): View
    {
        $car->load(['photos', 'inspections.inspector', 'offers.customer']);


        return view('admin.cars.show', compact('car'));
    }


    public function edit(CarUnit $car): View
    {
        $this->authorize('update', $car);
        $car->load('photos');

        return view('admin.cars.edit', compact('car'));
    }


    public function update(UpdateCarUnitRequest $request, CarUnit $car): RedirectResponse
    {
        $this->carUnitService->update($car, $request->validated());


        // Upload photos baru jika ada
        if ($request->hasFile('photos')) {
            $this->carUnitService->uploadPhotos($car, $request->file('photos'), false);
        }


        return redirect()
            ->route('admin.cars.show', $car)
            ->with('success', 'Unit mobil berhasil diperbarui.');
    }


    public function destroy(CarUnit $car): RedirectResponse
    {
        $this->authorize('delete', $car);


        $car->delete();


        return redirect()
            ->route('admin.cars.index')
            ->with('success', 'Unit mobil berhasil dihapus.');
    }


    public function updateStatus(Request $request, CarUnit $car): RedirectResponse
    {
        $this->authorize('update', $car);


        $request->validate([
            'status' => ['required', 'in:' . implode(',', array_column(CarUnitStatus::cases(), 'value'))],
        ]);


        $this->carUnitService->updateStatus($car, CarUnitStatus::from($request->status));


        return redirect()
            ->route('admin.cars.show', $car)
            ->with('success', 'Status unit berhasil diperbarui.');
    }


    public function setFeatured(CarUnit $car): RedirectResponse
    {
        $this->authorize('update', $car);


        $this->carUnitService->markAsFeatured($car);


        return redirect()
            ->route('admin.cars.show', $car)
            ->with('success', 'Unit ditandai sebagai featured.');
    }


    public function removeFeatured(CarUnit $car): RedirectResponse
    {
        $this->authorize('update', $car);


        $this->carUnitService->removeFeatured($car);


        return redirect()
            ->route('admin.cars.show', $car)
            ->with('success', 'Unit dihapus dari featured.');
    }

    public function uploadPhotos(Request $request, CarUnit $car): RedirectResponse
    {
        $this->authorize('update', $car);

        $request->validate([
            'photos' => ['required', 'array', 'min:1'],
            'photos.*' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:10240'],
        ], [
            'photos.required' => 'Pilih minimal satu file foto.',
            'photos.*.image' => 'File harus berupa gambar.',
            'photos.*.mimes' => 'Format foto harus JPEG, PNG, JPG, atau WEBP.',
            'photos.*.max' => 'Ukuran foto maksimal 10 MB per file.',
        ]);

        $this->carUnitService->uploadPhotos($car, $request->file('photos'), false);

        return back()->with('success', 'Foto baru berhasil diunggah.');
    }

    public function deletePhoto(CarUnit $car, CarPhoto $photo): RedirectResponse
    {
        $this->authorize('update', $car);

        if ($photo->car_unit_id !== $car->id) {
            abort(404, 'Foto tidak sesuai dengan unit mobil ini.');
        }

        $this->carUnitService->deletePhoto($photo);

        return back()->with('success', 'Foto berhasil dihapus.');
    }

    public function setPrimaryPhoto(CarUnit $car, CarPhoto $photo): RedirectResponse
    {
        $this->authorize('update', $car);

        if ($photo->car_unit_id !== $car->id) {
            abort(404, 'Foto tidak sesuai dengan unit mobil ini.');
        }

        $this->carUnitService->setPrimaryPhoto($photo);

        return back()->with('success', 'Foto utama berhasil diperbarui.');
    }

    public function replacePhoto(Request $request, CarUnit $car, CarPhoto $photo): RedirectResponse
    {
        $this->authorize('update', $car);

        if ($photo->car_unit_id !== $car->id) {
            abort(404, 'Foto tidak sesuai dengan unit mobil ini.');
        }

        $request->validate([
            'photo' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:10240'],
        ], [
            'photo.required' => 'Pilih file foto pengganti.',
            'photo.image' => 'File harus berupa gambar.',
            'photo.mimes' => 'Format foto harus JPEG, PNG, JPG, atau WEBP.',
            'photo.max' => 'Ukuran foto maksimal 10 MB.',
        ]);

        // Hapus file lama dari disk
        Storage::disk('public')->delete($photo->path);

        // Upload file baru
        $newPath = $request->file('photo')->store('cars/' . $car->id, 'public');
        $photo->update(['path' => $newPath]);

        return back()->with('success', 'Foto berhasil diganti dengan yang baru.');
    }
}



