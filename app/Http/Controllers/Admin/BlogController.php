<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BlogController extends Controller
{
    private const UPLOAD_DIR = 'uploads/blogs';

    public function index(): View
    {
        $blogs = Blog::query()
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->paginate(15);

        return view('admin.pages.blogs.index', compact('blogs'));
    }

    public function create(): View
    {
        return view('admin.pages.blogs.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules());
        $validated['slug'] = $this->makeSlug($validated['slug'] ?? $validated['title']);

        if (Blog::query()->where('slug', $validated['slug'])->exists()) {
            return back()
                ->withErrors(['slug' => 'This slug is already in use.'])
                ->withInput();
        }

        if ($request->hasFile('image')) {
            $validated['image'] = $this->storeImage($request);
        }

        $validated['status'] = $request->boolean('status');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        $validated['schema_type'] = 'BlogPosting';
        $faqItems = $this->faqItems($request);
        $validated['faq_schema'] = json_encode($faqItems, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        $validated['schema_markup'] = $this->schemaMarkup($validated, $faqItems);

        $blog = Blog::query()->create($validated);
        $this->syncMetaTags($blog);

        return redirect()
            ->route('blogs.index')
            ->with('success', 'Blog created successfully.');
    }

    public function edit(Blog $blog): View
    {
        return view('admin.pages.blogs.edit', compact('blog'));
    }

    public function update(Request $request, Blog $blog): RedirectResponse
    {
        $validated = $request->validate($this->rules($blog));
        $validated['slug'] = $this->makeSlug($validated['slug'] ?? $validated['title']);

        if (Blog::query()->where('slug', $validated['slug'])->whereKeyNot($blog->id)->exists()) {
            return back()
                ->withErrors(['slug' => 'This slug is already in use.'])
                ->withInput();
        }

        if ($request->hasFile('image')) {
            $this->deleteImage($blog->image);
            $validated['image'] = $this->storeImage($request);
        }

        $validated['status'] = $request->boolean('status');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        $validated['schema_type'] = 'BlogPosting';
        $faqItems = $this->faqItems($request);
        $validated['faq_schema'] = json_encode($faqItems, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        $schemaSource = array_merge($blog->toArray(), $validated);
        $validated['schema_markup'] = $this->schemaMarkup($schemaSource, $faqItems);

        $blog->update($validated);
        $this->syncMetaTags($blog);

        return redirect()
            ->route('blogs.index')
            ->with('success', 'Blog updated successfully.');
    }

    public function destroy(Blog $blog): RedirectResponse
    {
        $blog->delete();

        return redirect()
            ->route('blogs.index')
            ->with('success', 'Blog moved to bin successfully.');
    }

    private function rules(?Blog $blog = null): array
    {
        return [
            'title' => ['required', 'string', 'max:180'],
            'slug' => [
                'nullable',
                'string',
                'max:200',
                Rule::unique('blogs', 'slug')->ignore($blog),
            ],
            'category' => ['nullable', 'string', 'max:140'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:4096'],
            'image_alt' => ['nullable', 'string', 'max:180'],
            'description' => ['nullable', 'string'],
            'meta_keyword' => ['nullable', 'string'],
            'meta_description' => ['nullable', 'string'],
            'meta_tags' => ['nullable', 'string'],
            'schema_type' => ['nullable', 'string', 'max:80'],
            'faq_questions' => ['nullable', 'array'],
            'faq_questions.*' => ['nullable', 'string', 'max:300'],
            'faq_answers' => ['nullable', 'array'],
            'faq_answers.*' => ['nullable', 'string'],
            'publish_status' => ['required', Rule::in(['draft', 'posted'])],
            'status' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    private function syncMetaTags(Blog $blog): void
    {
        foreach ($this->tagList($blog->meta_tags) as $tagText) {
            $slug = $this->makeSlug($tagText);

            if (! $slug) {
                continue;
            }

            Tag::withTrashed()->updateOrCreate(
                ['slug' => $slug],
                [
                    'title' => $tagText,
                    'description' => $blog->description,
                    'meta_description' => $blog->meta_description ?: strip_tags((string) $blog->description),
                    'status' => true,
                ]
            )->restore();
        }
    }

    private function faqItems(Request $request): array
    {
        $questions = $request->input('faq_questions', []);
        $answers = $request->input('faq_answers', []);

        return collect($questions)
            ->map(function (?string $question, int $index) use ($answers) {
                return [
                    'question' => trim((string) $question),
                    'answer' => trim((string) ($answers[$index] ?? '')),
                ];
            })
            ->filter(fn (array $item) => $item['question'] !== '' && $item['answer'] !== '')
            ->values()
            ->all();
    }

    private function schemaMarkup(array $data, array $faqItems): string
    {
        $description = strip_tags(($data['meta_description'] ?? '') ?: ($data['description'] ?? ''));
        $image = $data['image'] ?? null;
        $blogPosting = [
            '@type' => 'BlogPosting',
            '@id' => url('/blogs/' . ($data['slug'] ?? '')) . '#blogposting',
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => url('/blogs/' . ($data['slug'] ?? '')),
            ],
            'headline' => $data['title'] ?? '',
            'description' => Str::limit($description, 500, ''),
            'image' => $image ? asset($image) : asset('asset/logo.png'),
            'articleSection' => $data['category'] ?? 'Orthopedic Care',
            'keywords' => $this->tagList($data['meta_tags'] ?? ''),
            'datePublished' => $this->schemaDate($data['created_at'] ?? null),
            'dateModified' => $this->schemaDate($data['updated_at'] ?? null),
            'author' => [
                '@type' => 'Organization',
                'name' => config('app.name'),
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => config('app.name'),
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => asset('asset/logo.png'),
                ],
            ],
        ];

        $schema = [
            '@context' => 'https://schema.org',
            '@graph' => [$blogPosting],
        ];

        if ($faqItems !== []) {
            $schema['@graph'][] = [
                '@type' => 'FAQPage',
                '@id' => url('/blogs/' . ($data['slug'] ?? '')) . '#faq',
                'mainEntity' => collect($faqItems)
                    ->map(fn (array $item) => [
                        '@type' => 'Question',
                        'name' => $item['question'],
                        'acceptedAnswer' => [
                            '@type' => 'Answer',
                            'text' => strip_tags($item['answer']),
                        ],
                    ])
                    ->values()
                    ->all(),
            ];
        }

        return json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    private function schemaDate(mixed $value): string
    {
        if ($value instanceof Carbon) {
            return $value->toIso8601String();
        }

        if ($value) {
            return Carbon::parse($value)->toIso8601String();
        }

        return now()->toIso8601String();
    }

    private function tagList(?string $tags): array
    {
        return collect(preg_split('/[\n,]+/', (string) $tags))
            ->map(fn (string $tag) => trim($tag))
            ->filter()
            ->unique(fn (string $tag) => Str::lower($tag))
            ->values()
            ->all();
    }

    private function makeSlug(string $value): string
    {
        return Str::slug($value);
    }

    private function storeImage(Request $request): string
    {
        $file = $request->file('image');
        $fileName = time() . '-' . uniqid('blog-', true) . '.' . $file->getClientOriginalExtension();
        $destination = public_path(self::UPLOAD_DIR);

        if (! File::exists($destination)) {
            File::makeDirectory($destination, 0755, true);
        }

        $file->move($destination, $fileName);

        return self::UPLOAD_DIR . '/' . $fileName;
    }

    private function deleteImage(?string $path): void
    {
        if (! $path) {
            return;
        }

        $absolutePath = public_path($path);

        if (File::exists($absolutePath)) {
            File::delete($absolutePath);
        }
    }
}
