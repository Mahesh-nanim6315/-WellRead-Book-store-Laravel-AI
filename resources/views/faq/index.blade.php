@include('common.header')


<div class="faq-container" style="margin-top: 100px;">
    <h1>Frequently Asked Questions</h1>

    @foreach($faqs as $faq)
        <div class="faq-item">
            <h3 class="faq-question">
                {{ $faq['question'] }}
            </h3>
            <p class="faq-answer">
                {{ $faq['answer'] }}
            </p>
        </div>
    @endforeach
</div>

@include('common.footer')
