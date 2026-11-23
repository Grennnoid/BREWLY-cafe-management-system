<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\Category;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::withCount('menus')->get();
        $selectedCategory = $request->query('category');
        $search = $request->query('search');

        $query = Menu::with('category')->orderBy('created_at', 'desc');

        if ($selectedCategory) {
            $query->where('category_id', $selectedCategory);
        }

        if ($search) {
            $query->where('name', 'like', "%{$search}%");
        }

        $menus = $query->paginate(12);

        return view('admin.menu.index', compact('menus', 'categories', 'selectedCategory', 'search'));
    }

    public function storeMenu(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,category_id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image_url' => 'nullable|url|max:500',
            'is_available' => 'nullable|boolean',
        ]);

        $validated['is_available'] = $request->has('is_available') ? true : false;

        Menu::create($validated);

        return back()->with('success', "Menu '{$validated['name']}' berhasil ditambahkan.");
    }

    public function updateMenu(Request $request, $id)
    {
        $menu = Menu::findOrFail($id);

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,category_id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image_url' => 'nullable|url|max:500',
            'is_available' => 'nullable|boolean',
        ]);

        $validated['is_available'] = $request->has('is_available');

        $menu->update($validated);

        return back()->with('success', "Menu '{$menu->name}' berhasil diperbarui.");
    }

    public function toggleAvailability($id)
    {
        $menu = Menu::findOrFail($id);
        $menu->is_available = !$menu->is_available;
        $menu->save();

        $statusStr = $menu->is_available ? 'Tersedia' : 'Habis / Tidak Tersedia';
        return back()->with('success', "Status menu '{$menu->name}' diubah menjadi: {$statusStr}.");
    }

    public function destroyMenu($id)
    {
        $menu = Menu::findOrFail($id);
        $name = $menu->name;
        $menu->delete();

        return back()->with('success', "Menu '{$name}' telah dihapus.");
    }

    // Category CRUD
    public function categories()
    {
        $categories = Category::withCount('menus')->get();
        return view('admin.menu.categories', compact('categories'));
    }

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Category::create($validated);

        return back()->with('success', "Kategori '{$validated['name']}' berhasil ditambahkan.");
    }

    public function updateCategory(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $category->update($validated);

        return back()->with('success', "Kategori '{$category->name}' berhasil diperbarui.");
    }

    public function destroyCategory($id)
    {
        $category = Category::findOrFail($id);
        $name = $category->name;
        $category->delete();

        return back()->with('success', "Kategori '{$name}' dan menu terkait telah dihapus.");
    }
}

