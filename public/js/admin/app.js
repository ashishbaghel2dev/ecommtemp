// app.js




document.addEventListener('DOMContentLoaded', () => {

    initSidebar();
    initHeaderDropdown();
    initBlogFaqBuilder();
    

});

function initBlogFaqBuilder() {
    document.querySelectorAll('[data-blog-faq-builder]').forEach((builder) => {
        const list = builder.querySelector('[data-faq-list]');
        const addButton = builder.querySelector('[data-add-faq]');
        const form = builder.closest('form');
        const preview = form ? form.querySelector('[data-schema-preview]') : null;

        if (!list || !addButton) {
            return;
        }

        const updateNumbers = () => {
            list.querySelectorAll('[data-faq-item]').forEach((item, index) => {
                const title = item.querySelector('.blog-faq-item-head strong');
                if (title) {
                    title.textContent = `Question #${index + 1}`;
                }
            });
        };

        const updatePreview = () => {
            if (!preview || !form) {
                return;
            }

            const title = form.querySelector('[name="title"]')?.value || '';
            const slug = form.querySelector('[name="slug"]')?.value || slugify(title);
            const description = form.querySelector('[name="meta_description"]')?.value
                || form.querySelector('[name="description"]')?.value
                || '';
            const questions = [...form.querySelectorAll('[name="faq_questions[]"]')];
            const answers = [...form.querySelectorAll('[name="faq_answers[]"]')];
            const faqItems = questions.map((question, index) => ({
                question: question.value.trim(),
                answer: (answers[index]?.value || '').trim(),
            })).filter((item) => item.question && item.answer);

            const graph = [{
                '@type': 'BlogPosting',
                headline: title,
                description: stripHtml(description),
                mainEntityOfPage: slug ? `/blogs/${slug}` : '',
            }];

            if (faqItems.length) {
                graph.push({
                    '@type': 'FAQPage',
                    mainEntity: faqItems.map((item) => ({
                        '@type': 'Question',
                        name: item.question,
                        acceptedAnswer: {
                            '@type': 'Answer',
                            text: stripHtml(item.answer),
                        },
                    })),
                });
            }

            preview.value = JSON.stringify({
                '@context': 'https://schema.org',
                '@graph': graph,
            }, null, 2);
        };

        const makeItem = () => {
            const item = document.createElement('div');
            item.className = 'blog-faq-item';
            item.setAttribute('data-faq-item', '');
            item.innerHTML = `
                <div class="blog-faq-item-head">
                    <strong>Question</strong>
                    <button type="button" class="banner-icon-btn blog-remove-faq-btn" data-remove-faq aria-label="Remove FAQ">
                        <i class="ti ti-x"></i>
                    </button>
                </div>
                <label class="banner-field">
                    <span>Question</span>
                    <input type="text" name="faq_questions[]" value="" placeholder="What is Mediclaim insurance?">
                </label>
                <label class="banner-field">
                    <span>Answer</span>
                    <textarea name="faq_answers[]" placeholder="Write answer"></textarea>
                </label>
            `;
            return item;
        };

        addButton.addEventListener('click', () => {
            list.appendChild(makeItem());
            updateNumbers();
            updatePreview();
        });

        list.addEventListener('click', (event) => {
            const button = event.target.closest('[data-remove-faq]');
            if (!button) {
                return;
            }

            if (list.querySelectorAll('[data-faq-item]').length === 1) {
                const item = button.closest('[data-faq-item]');
                item.querySelectorAll('input, textarea').forEach((field) => field.value = '');
            } else {
                button.closest('[data-faq-item]').remove();
            }

            updateNumbers();
            updatePreview();
        });

        if (form) {
            form.addEventListener('input', updatePreview);
        }

        updateNumbers();
        updatePreview();
    });
}

function slugify(value) {
    return value.toLowerCase().trim()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '');
}

function stripHtml(value) {
    const wrapper = document.createElement('div');
    wrapper.innerHTML = value;
    return (wrapper.textContent || wrapper.innerText || '').trim();
}
