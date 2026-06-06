<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\HomeCarouselImage;
use App\Models\ProductLabel;
use Illuminate\Http\Request;

class HomeCarouselImageController extends Controller
{
    public function index()
    {
        $carouselImages = collect($this->carouselDefinitions())
            ->map(function (array $definition) {
                return HomeCarouselImage::firstOrCreate(
                    ['carousel_key' => $definition['key']],
                    [
                        'title' => $definition['title'],
                        'image' => null,
                    ]
                );
            });

        return view('admin.pages.home_carousel_images.index', compact('carouselImages'));
    }

    public function update(Request $request, HomeCarouselImage $homeCarouselImage)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = [
            'title' => $request->title,
        ];

        if ($request->hasFile('image')) {
            if ($homeCarouselImage->image && file_exists(public_path($homeCarouselImage->image))) {
                unlink(public_path($homeCarouselImage->image));
            }

            $image = $request->file('image');
            $imageName = time() . '_carousel_' . $image->getClientOriginalName();
            $image->move(public_path('home-carousel-images'), $imageName);

            $data['image'] = 'home-carousel-images/' . $imageName;
        }

        $homeCarouselImage->update($data);

        return redirect()
            ->route('home-carousel-images.index')
            ->with('success', 'Carousel image updated successfully.');
    }

    private function carouselDefinitions(): array
    {
        $definitions = [
            [
                'key' => 'featured-products',
                'title' => 'Products You May Like',
            ],
        ];

        ProductLabel::query()
            ->whereIn('slug', ['new-arrived', 'best-product'])
            ->orderByRaw("FIELD(slug, 'new-arrived', 'best-product')")
            ->get()
            ->each(function (ProductLabel $label) use (&$definitions) {
                $definitions[] = [
                    'key' => 'label-' . $label->slug,
                    'title' => $label->name,
                ];
            });

        Category::query()
            ->whereIn('slug', ['cpu-accessories', 'wires-cables', 'network-adapters'])
            ->orderByRaw("FIELD(slug, 'cpu-accessories', 'wires-cables', 'network-adapters')")
            ->get()
            ->each(function (Category $category) use (&$definitions) {
                $definitions[] = [
                    'key' => 'category-' . $category->slug,
                    'title' => $category->name,
                ];
            });

        return $definitions;
    }
}
