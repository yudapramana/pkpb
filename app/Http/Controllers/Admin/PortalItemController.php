<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PortalItemRequest;
use App\Models\PortalItem;
use Illuminate\Support\Facades\Storage;

class PortalItemController extends Controller
{
    public function index()
    {
        return view('admin.portal-items.index', [
            'items' => PortalItem::ordered()->get(),
        ]);
    }

    public function create()
    {
        return view('admin.portal-items.form', [
            'item' => new PortalItem([
                'is_active' => true,
                'is_modal' => false,
                'sort_order' => ((int) PortalItem::max('sort_order')) + 10,
            ]),
        ]);
    }

    public function store(PortalItemRequest $request)
    {
        $item = PortalItem::create($request->itemData());
        $this->handleImage($request, $item);

        return redirect()->route('admin.portal-items.index')
            ->with('success', "Item {$item->title} berhasil ditambahkan.");
    }

    public function edit(PortalItem $portalItem)
    {
        return view('admin.portal-items.form', ['item' => $portalItem]);
    }

    public function update(PortalItemRequest $request, PortalItem $portalItem)
    {
        $portalItem->update($request->itemData());
        $this->handleImage($request, $portalItem);

        return redirect()->route('admin.portal-items.index')
            ->with('success', "Item {$portalItem->title} berhasil diperbarui.");
    }

    public function destroy(PortalItem $portalItem)
    {
        $title = $portalItem->title;

        if ($portalItem->image_path) {
            Storage::disk('public')->delete($portalItem->image_path);
        }
        $portalItem->delete();

        return redirect()->route('admin.portal-items.index')
            ->with('success', "Item {$title} berhasil dihapus.");
    }

    private function handleImage(PortalItemRequest $request, PortalItem $item)
    {
        $oldPath = $item->image_path;

        if ($request->hasFile('image')) {
            $item->update(['image_path' => $request->file('image')->store('portal-items', 'public')]);
        } elseif ($request->boolean('remove_image')) {
            $item->update(['image_path' => null]);
        }

        if ($oldPath && $oldPath !== $item->image_path) {
            Storage::disk('public')->delete($oldPath);
        }
    }
}
